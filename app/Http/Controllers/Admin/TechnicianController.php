<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicianForm;
use App\Models\TechnicianSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TechnicianController extends Controller
{
    public function index(): View
    {
        return view('admin.monteur.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = TechnicianForm::query()->with(['user:id,name,klantnummer', 'technician:id,name'])->latest('id');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('klantnummer', 'like', "%{$search}%"));
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
                'total' => TechnicianForm::count(),
                'paid' => TechnicianForm::where('payment_status', 'paid')->count(),
                'unpaid' => TechnicianForm::where('payment_status', 'unpaid')->count(),
            ],
        ]);
    }

    public function show(TechnicianForm $monteur): JsonResponse
    {
        $monteur->load(['technicianInvoice', 'user:id,name,email,klantnummer', 'technician:id,name']);

        return response()->json([
            'form' => [
                'id' => $monteur->id,
                'klantnummer' => $monteur->user?->klantnummer,
                'name' => $monteur->user?->name,
                'email' => $monteur->user?->email,
                'technician' => $monteur->technician?->name,
                'start_time' => substr((string) $monteur->start_time, 0, 5),
                'end_time' => substr((string) $monteur->end_time, 0, 5),
                'duration_minutes' => $monteur->duration_minutes,
                'quarter_count' => $monteur->quarter_count,
                'total' => $monteur->total,
                'member_discount' => $monteur->member_discount,
                'coupon_discount' => $monteur->coupon_discount,
                'payment_status' => $monteur->payment_status,
                'payment_method' => $monteur->payment_method,
                'description' => $monteur->description,
                'work_done' => $monteur->work_done,
                'advice' => $monteur->advice,
                'rating' => $monteur->rating,
                'comment' => $monteur->comment,
                'created_at' => $monteur->created_at?->format('d-m-Y H:i'),
                'invoice' => $monteur->technicianInvoice ? [
                    'id' => $monteur->technicianInvoice->id,
                    'invoice_number' => $monteur->technicianInvoice->invoice_number,
                    'invoice_date' => $monteur->technicianInvoice->invoice_date?->format('d-m-Y'),
                    'total' => $monteur->technicianInvoice->total,
                ] : null,
            ],
        ]);
    }

    public function invoiceDownload(TechnicianForm $monteur): BinaryFileResponse
    {
        $invoice = $monteur->technicianInvoice()->latest('id')->firstOrFail();
        abort_unless($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path), 404);

        return response()->download(
            Storage::disk('local')->path($invoice->pdf_path),
            'factuur-' . $invoice->invoice_number . '.pdf'
        );
    }

    public function destroy(TechnicianForm $monteur): JsonResponse
    {
        $invoice = $monteur->technicianInvoice()->latest('id')->first();
        if ($invoice?->pdf_path) {
            Storage::disk('local')->delete($invoice->pdf_path);
        }
        $monteur->delete();

        return response()->json(['message' => 'Factuur verwijderd.']);
    }

    public function rates(): View
    {
        return view('admin.monteur.rates', [
            'hour_price' => TechnicianSetting::getValue('hour_price') ?? 80,
            'travel_cost' => TechnicianSetting::getValue('travel_cost') ?? 5,
            'member_discount_type' => TechnicianSetting::getValue('member_discount_type') ?? 'none',
            'member_discount_value' => TechnicianSetting::getValue('member_discount_value') ?? 0,
            'member_free_travel' => (string) (TechnicianSetting::getValue('member_free_travel') ?? '0') === '1',
        ]);
    }

    public function updateRates(Request $request): JsonResponse
    {
        $data = $request->validate(
            [
                'hour_price' => ['required', 'numeric', 'min:1', 'max:9999'],
                'travel_cost' => ['required', 'numeric', 'min:0', 'max:9999'],
                'member_discount_type' => ['required', 'in:none,percent,fixed'],
                'member_discount_value' => ['required', 'numeric', 'min:0', 'max:9999'],
                'member_free_travel' => ['nullable', 'boolean'],
            ],
            [
                'hour_price.required' => 'Vul een uurtarief in.',
                'hour_price.min' => 'Het uurtarief moet minimaal €1 zijn.',
                'travel_cost.required' => 'Vul voorrijkosten in (0 mag).',
            ]
        );

        TechnicianSetting::setValue('hour_price', $data['hour_price']);
        TechnicianSetting::setValue('travel_cost', $data['travel_cost']);
        TechnicianSetting::setValue('member_discount_type', $data['member_discount_type']);
        TechnicianSetting::setValue('member_discount_value', $data['member_discount_value'] ?? 0);
        TechnicianSetting::setValue('member_free_travel', ! empty($data['member_free_travel']) ? '1' : '0');

        return response()->json(['message' => 'Tarieven bijgewerkt.']);
    }
}
