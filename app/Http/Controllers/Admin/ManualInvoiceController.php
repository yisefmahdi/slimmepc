<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManualInvoiceRequest;
use App\Mail\ManualInvoiceMail;
use App\Models\ManualInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ManualInvoiceController extends Controller
{
    /**
     * Show hardware invoices list.
     */
    public function index(): View
    {
        return view('admin.bevestiging-mail.hardware.index');
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.bevestiging-mail.hardware.create');
    }

    /**
     * Data for DataTables-like list with search & pagination.
     */
    public function data(Request $request): JsonResponse
    {
        $query = ManualInvoice::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%")
                    ->orWhere('device_info', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 15), 50);

        $paginator = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['id', 'invoice_number', 'name', 'email', 'device_info', 'description', 'subtotal', 'total', 'created_at']);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'current' => $paginator->currentPage(),
                'last' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    /**
     * Store and send invoice.
     */
    public function store(StoreManualInvoiceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Inclusief: Totaal is handmatig ingevuld (bron van waarheid)
        $total = isset($validated['total']) && $validated['total'] !== '' && $validated['total'] !== null ? round((float) $validated['total'], 2) : round((float) $validated['subtotal'], 2);
        // BTW wordt uit Totaal berekend: bij 21% is BTW = totaal * 21 / 121
        $taxPct = $validated['tax_percentage'] ?? 0;
        $taxAmount = $taxPct > 0 ? round($total * $taxPct / (100 + $taxPct), 2) : 0.00;
        // Subtotaal is netto (excl. btw) = totaal - btw
        $subtotal = round($total - $taxAmount, 2);

        // Generate unique invoice number like SLM-ECZ432 (SLM- + 6 alphanum)
        $invoiceNumber = $this->generateInvoiceNumber();

        $invoice = ManualInvoice::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'invoice_number' => $invoiceNumber,
            'device_info' => $validated['device_info'] ?? null,
            'description' => $validated['description'] ?? null,
            'subtotal' => $subtotal,
            'tax_percentage' => $taxPct,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.hardware', ['invoice' => $invoice]);
        $pdf->setPaper('a4', 'portrait');

        $pdfDir = 'invoices';
        $pdfFile = $pdfDir . '/' . $invoiceNumber . '.pdf';

        Storage::disk('local')->makeDirectory($pdfDir);
        Storage::disk('local')->put($pdfFile, $pdf->output());

        $invoice->update(['pdf_path' => $pdfFile]);

        // Send email with PDF attached (afterResponse like other mails)
        dispatch(function () use ($invoice) {
            Mail::to($invoice->email)->send(new ManualInvoiceMail($invoice));
        })->afterResponse();

        return response()->json([
            'message' => 'Factuur succesvol aangemaakt en verzonden naar ' . $invoice->email,
            'invoice' => $invoice,
        ], 201);
    }

    /**
     * Download PDF.
     */
    public function download(ManualInvoice $invoice): BinaryFileResponse
    {
        abort_if(!$invoice->pdf_path || !Storage::disk('local')->exists($invoice->pdf_path), 404, 'PDF niet gevonden');

        return response()->download(
            Storage::disk('local')->path($invoice->pdf_path),
            $invoice->invoice_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Delete invoice and PDF.
     */
    public function destroy(ManualInvoice $invoice): JsonResponse
    {
        if ($invoice->pdf_path) {
            Storage::disk('local')->delete($invoice->pdf_path);
        }
        $invoice->delete();

        return response()->json(['message' => 'Factuur verwijderd.']);
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $suffix = strtoupper(Str::random(6));
            // Ensure only A-Z0-9 (Str::random includes letters)
            $suffix = preg_replace('/[^A-Z0-9]/', 'A', $suffix);
            $candidate = 'SLM-' . $suffix;
        } while (ManualInvoice::where('invoice_number', $candidate)->exists());

        return $candidate;
    }
}
