<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Services\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Order::with(['billingAddress'])->orderByDesc('created_at')->orderByDesc('id');

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('klantnummer', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('order_status')) {
            $query->where('order_status', $status);
        }
        if ($pay = $request->input('payment_status')) {
            $query->where('payment_status', $pay);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()->map(fn (Order $o) => [
            'id' => $o->id,
            'order_number' => $o->order_number,
            'customer' => $o->billingAddress?->fullName() ?? $o->customer_email,
            'email' => $o->customer_email,
            'total' => $o->total_price,
            'shipping_method' => $o->shipping_method,
            'payment_status' => $o->payment_status,
            'order_status' => $o->order_status,
            'created_at' => $o->created_at?->format('d-m-Y H:i'),
        ]);

        return response()->json([
            'data' => $items,
            'counts' => [
                'pending' => Order::where('order_status', 'pending')->count(),
                'paid' => Order::where('payment_status', 'paid')->count(),
                'total' => Order::count(),
            ],
            'pagination' => [
                'current' => $paginator->currentPage(),
                'last' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'billingAddress', 'coupon', 'invoice', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        $changed = $order->order_status !== $data['order_status'];
        $order->update(['order_status' => $data['order_status']]);

        // Notify the customer by mail on every real status change (after response)
        if ($changed) {
            dispatch(function () use ($order) {
                Mail::to($order->fresh()->customer_email)->send(new OrderStatusMail($order->fresh()));
            })->afterResponse();
        }

        return response()->json(['message' => 'Status bijgewerkt.', 'order_status' => $order->order_status]);
    }

    public function destroy(Order $order): JsonResponse
    {
        if ($order->invoice?->pdf_path) {
            Storage::disk('local')->delete($order->invoice->pdf_path);
        }
        $order->delete();

        return response()->json(['message' => 'Bestelling verwijderd.']);
    }

    /**
     * Download the invoice PDF, always (re)generated from the current template
     * so the file always matches the latest design. Use this when the stored
     * file is outdated (e.g. after a design change or when the browser keeps
     * showing a cached copy).
     */
    public function invoiceRegenerate(Order $order, OrderPaymentService $payments): BinaryFileResponse
    {
        $invoice = $order->invoice;
        abort_if(! $invoice, 404);

        if ($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path)) {
            Storage::disk('local')->delete($invoice->pdf_path);
        }

        $invoice = $payments->ensurePdf($invoice);

        return $this->downloadInvoicePdf($invoice);
    }

    private function downloadInvoicePdf(OrderInvoice $invoice): BinaryFileResponse
    {
        // Unieke bestandsnaam per download (met datum/tijd), zodat een nieuw
        // gedownload exemplaar nooit verward kan worden met een eerder
        // gedownload (oud) bestand met dezelfde naam in de Downloads-map.
        $filename = $invoice->invoice_number.'-'.now()->format('Ymd-His').'.pdf';

        return response()->download(
            Storage::disk('local')->path($invoice->pdf_path),
            $filename,
            [
                'Content-Type' => 'application/pdf',
                // Nooit een verouderde PDF uit de browser-cache tonen na regeneratie
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }

    public function newCount(): JsonResponse
    {
        return response()->json(['count' => Order::where('order_status', 'pending')->count()]);
    }
}
