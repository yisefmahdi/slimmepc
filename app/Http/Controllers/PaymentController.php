<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use App\Services\Payments\MolliePaymentService;
use App\Support\Cms;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected MolliePaymentService $payments,
        protected OrderPaymentService $orderPayments,
    ) {}

    /** Mollie server-to-server callback — source of truth. */
    public function webhook(Request $request)
    {
        $paymentId = (string) $request->input('id', '');
        if ($paymentId === '' || ! $this->payments->isConfigured()) {
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            $payment = $this->payments->getPayment($paymentId);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['status' => 'retry'], 500);
        }

        $orderId = (int) ($payment->metadata->order_id ?? 0);
        $order = $orderId ? Order::find($orderId) : null;
        if (! $order) {
            return response()->json(['status' => 'unknown-order'], 200);
        }

        if ($this->payments->isPaid($payment)) {
            // Never trust a paid flag alone: the collected amount must equal
            // the order total, otherwise an underpaid order would be fulfilled.
            if (! $this->payments->amountMatches($payment, (float) $order->total_price)) {
                report(new \RuntimeException('Mollie amount mismatch for order '.$order->id));
                $order->update(['payment_status' => 'failed']);

                return response()->json(['status' => 'amount-mismatch'], 200);
            }
            $this->orderPayments->finalizeOrder($order, $payment->method ?? null);
        } else {
            $status = (string) ($payment->status ?? '');
            if (in_array($status, ['canceled', 'expired', 'failed'], true)) {
                $order->update(['payment_status' => 'failed']);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /** Customer returns from Mollie — verify live and show result. */
    public function return(Request $request, int $order)
    {
        $orderModel = Order::with(['items', 'billingAddress'])->findOrFail($order);

        if ($orderModel->payment_status !== 'paid' && $orderModel->mollie_payment_id && $this->payments->isConfigured()) {
            try {
                $payment = $this->payments->getPayment($orderModel->mollie_payment_id);
                if ($this->payments->isPaid($payment)
                    && $this->payments->amountMatches($payment, (float) $orderModel->total_price)) {
                    $this->orderPayments->finalizeOrder($orderModel, $payment->method ?? null);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $orderModel->refresh();

        $c = Cms::page('home');
        $design = Cms::design();

        if ($orderModel->payment_status === 'paid') {
            // Only the buyer (or an admin) may see order details: sequential
            // ids are trivially enumerable, so strangers get the generic page.
            $viewModel = $this->mayViewOrder($request, $orderModel) ? $orderModel : null;

            return view('landing.payment-success', ['c' => $c, 'design' => $design, 'orderModel' => $viewModel]);
        }

        return redirect()->route('payment.failed', ['order' => $orderModel->id]);
    }

    /**
     * Whether the current visitor may see this order's details.
     * Registered orders are bound to their owner; guest orders to the
     * checkout session that created them (Mollie redirects back into the
     * same browser session). Admins may always view.
     */
    protected function mayViewOrder(Request $request, Order $order): bool
    {
        $user = $request->user();
        if ($user && $user->isAdmin()) {
            return true;
        }
        if ($order->user_id) {
            return $user !== null && (int) $user->id === (int) $order->user_id;
        }

        return in_array($order->id, (array) $request->session()->get('owned_orders', []), false);
    }

    public function success(Request $request)
    {
        $orderModel = null;
        if ($request->has('order')) {
            $candidate = Order::with(['items'])->find($request->input('order'));
            // Never celebrate (or leak details of) an unpaid order, and only
            // show details the visitor is entitled to see.
            if ($candidate && $candidate->payment_status === 'paid' && $this->mayViewOrder($request, $candidate)) {
                $orderModel = $candidate;
            }
        }

        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.payment-success', compact('c', 'design', 'orderModel'));
    }

    public function failed(Request $request)
    {
        $orderModel = null;
        if ($request->has('order')) {
            $candidate = Order::with(['items'])->find($request->input('order'));
            if ($candidate && $this->mayViewOrder($request, $candidate)) {
                $orderModel = $candidate;
            }
        }

        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.payment-failed', compact('c', 'design', 'orderModel'));
    }
}
