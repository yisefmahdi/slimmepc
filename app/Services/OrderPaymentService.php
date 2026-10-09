<?php

namespace App\Services;

use App\Mail\AdminOrderNotificationMail;
use App\Mail\LicenseShortageMail;
use App\Mail\OrderInvoiceMail;
use App\Models\Cart;
use App\Models\CouponUsage;
use App\Models\LicenseCode;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Services\AdminPushNotifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // Serialize concurrent finalizations (double webhook + return race)
        // on the order row: the paid-check and all side effects below run
        // atomically, so a second caller always sees payment_status=paid
        // and becomes a no-op instead of duplicating invoices/licences.
        $finalized = DB::transaction(function () use ($order, $mollieMethod) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            if (! $locked || $locked->payment_status === 'paid') {
                return null;
            }

            $locked->update([
                'payment_status' => 'paid',
                'order_status' => $locked->order_status === 'pending' ? 'processing' : $locked->order_status,
                'payment_method' => $mollieMethod ?? $locked->payment_method,
            ]);

            // Coupon usage bookkeeping
            if ($locked->coupon_id) {
                $locked->coupon?->increment('used_count');
                CouponUsage::firstOrCreate(
                    [
                        'coupon_id' => $locked->coupon_id,
                        'user_id' => $locked->user_id,
                        'guest_token' => $locked->user_id ? null : ('order-'.$locked->id),
                    ],
                    ['used_at' => now()]
                );
            }

            // Digital products: assign one license code per purchased unit.
            // Runs inside a transaction with row locks so concurrent webhooks
            // can never hand out the same code twice. Returns per-product
            // shortages (empty when everything was served).
            $licenseShortages = $this->assignLicenseCodes($locked);
            if ($licenseShortages !== []) {
                Log::warning('License pool ran out during finalize', ['order_id' => $locked->id, 'shortages' => $licenseShortages]);
            }

            $invoice = $this->ensureInvoice($locked);
            $this->ensurePdf($invoice);

            return ['order' => $locked->fresh(), 'invoice' => $invoice->fresh(), 'shortages' => $licenseShortages];
        });

        if ($finalized === null) {
            return;
        }

        ['order' => $order, 'invoice' => $invoice, 'shortages' => $licenseShortages] = $finalized;

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
            dispatch(function () use ($order, $notify, $licenseShortages) {
                Mail::to($notify)->send(new AdminOrderNotificationMail($order->fresh()));

                // Same moment as the admin e-mail: push to all admin devices.
                AdminPushNotifier::notify(
                    'order',
                    (string) $order->fresh()->order_number,
                    'Nieuwe bestelling: '.$order->fresh()->order_number,
                    'Totaal € '.number_format((float) $order->fresh()->total_price, 2, ',', '.'),
                    route('admin.orders.show', $order->fresh(), absolute: true)
                );

                // License pool ran dry mid-sale (race window): loud alert so an
                // admin refills the pool instead of silently completing.
                if ($licenseShortages !== []) {
                    Mail::to($notify)->send(new LicenseShortageMail($order->fresh(), $licenseShortages));
                    AdminPushNotifier::notify(
                        'license-shortage',
                        (string) $order->fresh()->order_number,
                        'Licentiecodes op: '.$order->fresh()->order_number,
                        'Bestelling betaald zonder (voldoende) codes — pool aanvullen.',
                        route('admin.orders.show', $order->fresh(), absolute: true)
                    );
                }
            })->afterResponse();
        }
    }

    /**
     * Assign available license codes to the digital items of a paid order.
     *
     * @return array<int, array{title: string, quantity: int, available: int}> per-product
     *         shortages (empty when every digital unit was served).
     */
    public function assignLicenseCodes(Order $order): array
    {
        $order->loadMissing(['items.product']);
        $shortages = [];

        DB::transaction(function () use ($order, &$shortages) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if (! $product || ! (bool) $product->is_digital) {
                    continue;
                }

                // Idempotency: codes already linked to this item (webhook + return race)
                $already = LicenseCode::where('order_item_id', $item->id)->count();
                $needed = max((int) $item->quantity - $already, 0);
                if ($needed <= 0) {
                    continue;
                }

                $codes = LicenseCode::where('product_id', $product->id)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->limit($needed)
                    ->get();

                if ($codes->count() < $needed) {
                    $shortages[] = [
                        'title' => $item->product_name ?: ($product->title ?? 'Onbekend product'),
                        'quantity' => (int) $item->quantity,
                        'available' => $codes->count() + $already,
                    ];
                }

                foreach ($codes as $code) {
                    $code->update([
                        'status' => 'sold',
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'assigned_at' => now(),
                    ]);
                }
            }
        });

        return $shortages;
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
