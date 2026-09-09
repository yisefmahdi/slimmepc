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
                if ($this->payments->isPaid($payment)) {
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
            return view('landing.payment-success', compact('c', 'design', 'orderModel'));
        }

        return redirect()->route('payment.failed', ['order' => $orderModel->id]);
    }

    public function success(Request $request)
    {
        $orderModel = null;
        if ($request->has('order')) {
            $orderModel = Order::with(['items'])->find($request->input('order'));
        }

        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.payment-success', compact('c', 'design', 'orderModel'));
    }

    public function failed(Request $request)
    {
        $orderModel = null;
        if ($request->has('order')) {
            $orderModel = Order::with(['items'])->find($request->input('order'));
        }

        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.payment-failed', compact('c', 'design', 'orderModel'));
    }
}
