<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MembershipController extends Controller
{
    public function index(): View
    {
        return view('admin.lidmaatschap.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Membership::query()->with('user:id,name,klantnummer')->latest('id');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('klantnummer', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('payment_status', $status);
        }

        $perPage = min((int) $request->input('per_page', 10), 50);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'current' => $paginator->currentPage(),
                'last' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
            'counts' => [
                'total' => Membership::count(),
                'paid' => Membership::where('payment_status', 'paid')->count(),
                'unpaid' => Membership::where('payment_status', 'unpaid')->count(),
            ],
        ]);
    }

    public function show(Membership $lidmaatschap): JsonResponse
    {
        $lidmaatschap->load(['invoices', 'user:id,name,email,klantnummer']);

        return response()->json([
            'membership' => [
                'id' => $lidmaatschap->id,
                'klantnummer' => $lidmaatschap->klantnummer,
                'customer_type' => $lidmaatschap->customer_type,
                'customer_gender' => $lidmaatschap->customer_gender,
                'name' => $lidmaatschap->name,
                'email' => $lidmaatschap->customer_email,
                'phone' => $lidmaatschap->customer_phone,
                'address' => $lidmaatschap->customer_address,
                'postcode' => $lidmaatschap->postcode,
                'city' => $lidmaatschap->city,
                'start_date' => $lidmaatschap->start_date?->format('d-m-Y'),
                'end_date' => $lidmaatschap->end_date?->format('d-m-Y'),
                'total' => $lidmaatschap->total,
                'payment_status' => $lidmaatschap->payment_status,
                'payment_method' => $lidmaatschap->payment_method,
                'created_at' => $lidmaatschap->created_at?->format('d-m-Y H:i'),
                'user' => $lidmaatschap->user ? [
                    'name' => $lidmaatschap->user->name,
                    'klantnummer' => $lidmaatschap->user->klantnummer,
                ] : null,
                'invoices' => $lidmaatschap->invoices->map(fn ($inv) => [
                    'id' => $inv->id,
                    'invoice_number' => $inv->invoice_number,
                    'invoice_date' => $inv->invoice_date?->format('d-m-Y'),
                    'total' => $inv->total,
                ]),
            ],
        ]);
    }

    public function invoiceDownload(Membership $lidmaatschap): BinaryFileResponse
    {
        $invoice = $lidmaatschap->invoices()->latest('id')->firstOrFail();
        abort_unless($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path), 404);

        return response()->download(
            Storage::disk('local')->path($invoice->pdf_path),
            'factuur-'.$invoice->invoice_number.'.pdf'
        );
    }

    public function destroy(Membership $lidmaatschap): JsonResponse
    {
        foreach ($lidmaatschap->invoices as $invoice) {
            if ($invoice->pdf_path) {
                Storage::disk('local')->delete($invoice->pdf_path);
            }
        }
        $lidmaatschap->delete();

        return response()->json(['message' => 'Lidmaatschap verwijderd.']);
    }

    public function settings(): View
    {
        return view('admin.lidmaatschap.settings', [
            'price' => MembershipSetting::price(),
        ]);
    }

    public function updatePrice(Request $request): JsonResponse
    {
        $data = $request->validate(
            ['subscription_price' => ['required', 'numeric', 'min:1', 'max:9999']],
            [
                'subscription_price.required' => 'Vul een prijs in.',
                'subscription_price.numeric' => 'De prijs moet een getal zijn.',
                'subscription_price.min' => 'De prijs moet minimaal €1 zijn.',
            ]
        );

        MembershipSetting::updateOrCreate(['id' => 1], $data);

        return response()->json([
            'message' => 'Lidmaatschapsprijs bijgewerkt.',
            'price' => (float) $data['subscription_price'],
        ]);
    }
}
