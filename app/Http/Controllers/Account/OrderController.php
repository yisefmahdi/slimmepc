<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public const ORDER_STATUS_LABELS = [
        'pending' => 'In afwachting',
        'processing' => 'In behandeling',
        'shipped' => 'Verzonden',
        'completed' => 'Afgerond',
        'cancelled' => 'Geannuleerd',
    ];

    public const ORDER_STATUS_STYLES = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
        'shipped' => 'bg-violet-50 text-violet-700 border-violet-200',
        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];

    public const PAYMENT_STATUS_LABELS = [
        'pending' => 'Nog niet betaald',
        'paid' => 'Betaald',
    ];

    public function index(Request $request)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        $orders = Order::withCount('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('landing.account.orders.index', [
            'c' => $c,
            'design' => $design,
            'orders' => $orders,
            'orderStatusLabels' => self::ORDER_STATUS_LABELS,
            'orderStatusStyles' => self::ORDER_STATUS_STYLES,
            'paymentStatusLabels' => self::PAYMENT_STATUS_LABELS,
        ]);
    }

    public function show(Request $request, string $orderNumber)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        $order = Order::with(['items.product', 'invoice', 'billingAddress'])
            ->where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('landing.account.orders.show', [
            'c' => $c,
            'design' => $design,
            'order' => $order,
            'orderStatusLabels' => self::ORDER_STATUS_LABELS,
            'orderStatusStyles' => self::ORDER_STATUS_STYLES,
            'paymentStatusLabels' => self::PAYMENT_STATUS_LABELS,
        ]);
    }

    public function invoice(Request $request, string $orderNumber): BinaryFileResponse
    {
        $order = Order::with('invoice')
            ->where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $invoice = $order->invoice;
        abort_if(! $invoice || ! $invoice->pdf_path || ! Storage::disk('local')->exists($invoice->pdf_path), 404);

        return response()->download(
            Storage::disk('local')->path($invoice->pdf_path),
            $invoice->invoice_number.'.pdf',
            [
                // Nooit een verouderde PDF uit de browser-cache tonen na regeneratie
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }
}
