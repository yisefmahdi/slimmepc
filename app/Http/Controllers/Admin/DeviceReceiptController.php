<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceReceiptRequest;
use App\Mail\DeviceReceiptMail;
use App\Models\DeviceReceipt;
use App\Models\DeviceReceiptPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $query = DeviceReceipt::query()->withCount('photos');
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
            ->paginate($perPage, ['id', 'customer_name', 'customer_email', 'device_type', 'phone_number', 'serial_number', 'notes', 'received_at', 'type', 'status', 'created_at']);

        $items = collect($paginator->items())->map(function ($row) {
            $arr = $row->toArray();
            $arr['receipt_number'] = $row->receiptNumber();
            $arr['photos_count'] = $row->photos_count ?? 0;
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

    public function show(DeviceReceipt $receipt): JsonResponse
    {
        $receipt->load('photos');

        return response()->json([
            'receipt' => array_merge($receipt->toArray(), ['receipt_number' => $receipt->receiptNumber()]),
            'photos' => $receipt->photoUrls(),
            'photos_count' => $receipt->photos->count(),
        ]);
    }

    public function updateStatus(Request $request, DeviceReceipt $receipt): JsonResponse
    {
        $request->validate(['status' => 'required|string|in:received,processing,completed']);

        $newStatus = $request->status;

        $receipt->update(['status' => $newStatus]);

        if ($newStatus === 'completed') {
            // Send email immediately to ensure delivery in local/production environments
            Mail::to($receipt->customer_email)->send(new \App\Mail\DeviceReceiptCompletedMail($receipt));
        }

        return response()->json(['message' => 'Status bijgewerkt succesvol.']);
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
            'status' => 'received',
        ]);

        if ($request->hasFile('photos')) {
            $sort = 0;
            foreach ((array) $request->file('photos') as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }
                $name = Str::uuid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('receipt/'.$receipt->id, $name, 'local');
                $receipt->photos()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'sort_order' => $sort++,
                ]);
            }
        }

        $receipt->loadCount('photos');

        dispatch(function () use ($receipt) {
            Mail::to($receipt->customer_email)->send(new DeviceReceiptMail($receipt));
        })->afterResponse();

        return response()->json([
            'message' => 'Ontvangstbevestiging succesvol aangemaakt en verzonden naar ' . $receipt->customer_email,
            'receipt' => array_merge($receipt->toArray(), ['receipt_number' => $receipt->receiptNumber()]),
            'photos_count' => $receipt->photos_count ?? 0,
        ], 201);
    }

    public function destroy(DeviceReceipt $receipt): JsonResponse
    {
        Storage::disk('local')->deleteDirectory('receipt/'.$receipt->id);

        $receipt->delete();

        return response()->json(['message' => 'Ontvangst verwijderd.']);
    }

    /**
     * Stream één ontvangstfoto (disk local, zelfde als repair inbox photo).
     */
    public function photo(DeviceReceipt $receipt, DeviceReceiptPhoto $photo): BinaryFileResponse
    {
        abort_unless($photo->device_receipt_id === $receipt->id, 404);
        abort_unless(Storage::disk('local')->exists($photo->path), 404);

        return response()->file(
            Storage::disk('local')->path($photo->path),
            ['Content-Disposition' => 'inline']
        );
    }

    /**
     * Verwijder één foto (rij + bestand).
     */
    public function destroyPhoto(DeviceReceipt $receipt, DeviceReceiptPhoto $photo): JsonResponse
    {
        abort_unless($photo->device_receipt_id === $receipt->id, 404);

        Storage::disk('local')->delete($photo->path);
        $photo->delete();

        return response()->json(['message' => 'Foto verwijderd.']);
    }
}
