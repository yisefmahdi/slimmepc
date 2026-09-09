<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $order->update(['order_status' => $data['order_status']]);

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

    public function invoiceDownload(Order $order): BinaryFileResponse
    {
        $invoice = $order->invoice;
        abort_if(! $invoice || ! $invoice->pdf_path || ! Storage::disk('local')->exists($invoice->pdf_path), 404);

        return response()->download(
            Storage::disk('local')->path($invoice->pdf_path),
            $invoice->invoice_number.'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function newCount(): JsonResponse
    {
        return response()->json(['count' => Order::where('order_status', 'pending')->count()]);
    }
}
