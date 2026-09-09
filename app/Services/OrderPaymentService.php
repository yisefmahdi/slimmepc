<?php

namespace App\Services;

use App\Mail\AdminOrderNotificationMail;
use App\Mail\OrderInvoiceMail;
use App\Models\Cart;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Idempotent order finalization after a confirmed Mollie payment.
 * Called from both the webhook (source of truth) and the return route.
 */
class OrderPaymentService
{
    public function finalizeOrder(Order $order, ?string $mollieMethod = null): void
    {
        $order->refresh();

        if ($order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status' => 'paid',
            'order_status' => $order->order_status === 'pending' ? 'processing' : $order->order_status,
            'payment_method' => $mollieMethod ?? $order->payment_method,
        ]);

        // Coupon usage bookkeeping
        if ($order->coupon_id) {
            $order->coupon?->increment('used_count');
            CouponUsage::firstOrCreate(
                [
                    'coupon_id' => $order->coupon_id,
                    'user_id' => $order->user_id,
                    'guest_token' => $order->user_id ? null : ('order-'.$order->id),
                ],
                ['used_at' => now()]
            );
        }

        $invoice = $this->ensureInvoice($order);
        $this->ensurePdf($invoice);

        // Clear the cart snapshot source
        $cartId = (int) ($order->cart_id ?? 0);
        if ($cartId) {
            Cart::where('id', $cartId)->first()?->items()->delete();
            Cart::where('id', $cartId)->first()?->update(['coupon_id' => null]);
        } elseif ($order->user_id) {
            Cart::where('user_id', $order->user_id)->first()?->items()->delete();
            Cart::where('user_id', $order->user_id)->first()?->update(['coupon_id' => null]);
        }

        $invoice->refresh();

        // Customer invoice mail (after response so Mollie gets a fast 200)
        dispatch(function () use ($order, $invoice) {
            Mail::to($order->customer_email)->send(new OrderInvoiceMail($invoice->fresh()));
        })->afterResponse();

        // Owner notification mail
        $notify = (string) (config('contact-inbox.notify_email') ?: env('CONTACT_NOTIFY_EMAIL', ''));
        if ($notify !== '') {
            dispatch(function () use ($order, $notify) {
                Mail::to($notify)->send(new AdminOrderNotificationMail($order->fresh()));
            })->afterResponse();
        }
    }

    public function ensureInvoice(Order $order): OrderInvoice
    {
        $existing = $order->invoice;
        if ($existing) {
            return $existing;
        }

        $address = $order->billingAddress;

        return OrderInvoice::create([
            'order_id' => $order->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoice_date' => now()->toDateString(),
            'customer_name' => trim(($address?->first_name ?? '').' '.($address?->last_name ?? '')) ?: $order->customer_email,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'street_address' => $address ? trim($address->street.' '.$address->house_number.($address->addition ? '-'.$address->addition : '')) : null,
            'postal_code' => $address?->postcode,
            'city' => $address?->city,
            'klantnummer' => $order->klantnummer,
            'subtotal' => $order->subtotal,
            'tax_percentage' => $order->tax_percentage,
            'tax_amount' => $order->tax_amount,
            'discount_amount' => $order->discount_amount,
            'shipping_cost' => $order->shipping_cost,
            'total' => $order->total_price,
            'payment_method' => $order->payment_method,
        ]);
    }

    public function ensurePdf(OrderInvoice $invoice): OrderInvoice
    {
        $invoice->refresh();
        if ($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path)) {
            return $invoice;
        }

        $pdf = Pdf::loadView('invoices.order', ['invoice' => $invoice->fresh('order.items')]);
        $pdf->setPaper('a4', 'portrait');

        Storage::disk('local')->makeDirectory('invoices/orders');
        $file = 'invoices/orders/'.$invoice->invoice_number.'.pdf';
        Storage::disk('local')->put($file, $pdf->output());

        $invoice->update(['pdf_path' => $file]);

        return $invoice->fresh();
    }

    protected function generateInvoiceNumber(): string
    {
        do {
            $candidate = 'INV-'.date('Y').'-'.strtoupper(Str::random(6));
            $candidate = preg_replace('/[^A-Z0-9-]/', 'A', $candidate);
        } while (OrderInvoice::where('invoice_number', $candidate)->exists());

        return $candidate;
    }
}
