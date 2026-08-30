<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceReceiptRequest;
use App\Mail\DeviceReceiptMail;
use App\Models\DeviceReceipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DeviceReceiptController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type', 'laptop');
        if (!in_array($type, ['laptop', 'ipad_iphone', 'playstation_xbox'], true)) {
            $type = 'laptop';
        }

        return view('admin.bevestiging-mail.ontvangst.index', ['type' => $type]);
    }

    public function create(Request $request): View
    {
        $type = $request->query('type', 'laptop');
        if (!in_array($type, ['laptop', 'ipad_iphone', 'playstation_xbox'], true)) {
            $type = 'laptop';
        }

        return view('admin.bevestiging-mail.ontvangst.create', ['type' => $type]);
    }

    public function data(Request $request): JsonResponse
    {
        $type = $request->query('type', $request->input('type'));
        $query = DeviceReceipt::query();
        if ($type && in_array($type, ['laptop', 'ipad_iphone', 'playstation_xbox'], true)) {
            $query->where('type', $type);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('device_type', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 15), 50);

        $paginator = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['id', 'customer_name', 'customer_email', 'device_type', 'phone_number', 'serial_number', 'notes', 'received_at', 'type', 'created_at']);

        $items = collect($paginator->items())->map(function ($row) {
            $arr = $row->toArray();
            $arr['receipt_number'] = $row->receiptNumber();
            return $arr;
        });

        return response()->json([
            'data' => $items,
            'pagination' => [
                'current' => $paginator->currentPage(),
                'last' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function store(StoreDeviceReceiptRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $receipt = DeviceReceipt::create([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'device_type' => $validated['device_type'],
            'phone_number' => $validated['phone_number'],
            'serial_number' => $validated['serial_number'] ?? null,
            'received_at' => $validated['received_at'],
            'notes' => $validated['notes'] ?? null,
            'type' => $validated['type'],
        ]);

        dispatch(function () use ($receipt) {
            Mail::to($receipt->customer_email)->send(new DeviceReceiptMail($receipt));
        })->afterResponse();

        return response()->json([
            'message' => 'Ontvangstbevestiging succesvol aangemaakt en verzonden naar ' . $receipt->customer_email,
            'receipt' => $receipt,
        ], 201);
    }

    public function destroy(DeviceReceipt $receipt): JsonResponse
    {
        $receipt->delete();

        return response()->json(['message' => 'Ontvangst verwijderd.']);
    }
}
