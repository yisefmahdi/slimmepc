<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLaptopLoanRequest;
use App\Mail\LaptopLoanMail;
use App\Mail\LaptopLoanReturnMail;
use App\Models\LaptopLoan;
use App\Models\LaptopLoanPhoto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaptopLoanController extends Controller
{
    public function index(): View
    {
        return view('admin.leen-huur.index');
    }

    public function create(): View
    {
        return view('admin.leen-huur.create', ['loan' => null]);
    }

    public function edit(LaptopLoan $loan): View
    {
        $loan->load('photos');

        return view('admin.leen-huur.create', ['loan' => $loan]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = LaptopLoan::query()->withCount('photos');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('laptop_type', 'like', "%{$search}%")
                    ->orWhere('repair_number', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->trim()->toString()) {
            if (in_array($status, ['uitgeleend', 'teruggebracht'], true)) {
                $query->where('status', $status);
            }
        }

        $perPage = min((int) $request->input('per_page', 15), 50);

        $paginator = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['id', 'customer_name', 'customer_email', 'phone', 'address', 'postcode', 'city', 'repair_number', 'laptop_type', 'given_at', 'status', 'created_at']);

        $items = collect($paginator->items())->map(function ($row) {
            $arr = $row->toArray();
            $arr['loan_number'] = $row->loanNumber();
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

    public function show(LaptopLoan $loan): JsonResponse
    {
        $loan->load('photos');

        return response()->json([
            'loan' => array_merge($loan->toArray(), ['loan_number' => $loan->loanNumber()]),
            'photos' => $loan->photoUrls(),
            'photos_count' => $loan->photos->count(),
        ]);
    }

    public function store(StoreLaptopLoanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $loan = LaptopLoan::create([
            'customer_name' => $validated['customer_name'],
            'address' => $validated['address'],
            'postcode' => $validated['postcode'],
            'city' => $validated['city'],
            'phone' => $validated['phone'],
            'customer_email' => $validated['customer_email'],
            'repair_number' => $validated['repair_number'] ?? null,
            'laptop_type' => $validated['laptop_type'],
            'given_at' => $validated['given_at'],
            'status' => 'uitgeleend',
        ]);

        $this->storePhotos($request, $loan);
        $loan->loadCount('photos');

        $this->generatePdf($loan);

        dispatch(function () use ($loan) {
            Mail::to($loan->customer_email)->send(new LaptopLoanMail($loan));
        })->afterResponse();

        return response()->json([
            'message' => 'Laptop succesvol uitgeleend. Bevestiging verzonden naar ' . $loan->customer_email,
            'loan' => array_merge($loan->toArray(), ['loan_number' => $loan->loanNumber()]),
            'photos_count' => $loan->photos_count ?? 0,
        ], 201);
    }

    public function update(StoreLaptopLoanRequest $request, LaptopLoan $loan): JsonResponse
    {
        $validated = $request->validated();

        $loan->update([
            'customer_name' => $validated['customer_name'],
            'address' => $validated['address'],
            'postcode' => $validated['postcode'],
            'city' => $validated['city'],
            'phone' => $validated['phone'],
            'customer_email' => $validated['customer_email'],
            'repair_number' => $validated['repair_number'] ?? null,
            'laptop_type' => $validated['laptop_type'],
            'given_at' => $validated['given_at'],
        ]);

        $this->storePhotos($request, $loan);
        $loan->loadCount('photos');

        $this->generatePdf($loan);

        return response()->json([
            'message' => 'Uitgifte succesvol bijgewerkt.',
            'loan' => array_merge($loan->toArray(), ['loan_number' => $loan->loanNumber()]),
            'photos_count' => $loan->photos_count ?? 0,
        ]);
    }

    /**
     * Markeer als teruggebracht (via bevestigings-popup) + stuur retour-mail.
     */
    public function markReturned(LaptopLoan $loan): JsonResponse
    {
        if ($loan->status === 'teruggebracht') {
            return response()->json(['message' => 'Deze laptop is al teruggebracht.'], 422);
        }

        $loan->update(['status' => 'teruggebracht']);

        dispatch(function () use ($loan) {
            Mail::to($loan->customer_email)->send(new LaptopLoanReturnMail($loan));
        })->afterResponse();

        return response()->json([
            'message' => 'Laptop gemarkeerd als teruggebracht. Bevestiging verzonden naar ' . $loan->customer_email,
            'loan' => array_merge($loan->toArray(), ['loan_number' => $loan->loanNumber()]),
        ]);
    }

    public function destroy(LaptopLoan $loan): JsonResponse
    {
        Storage::disk('local')->deleteDirectory('leen-huur/' . $loan->id);
        if ($loan->pdf_path) {
            Storage::disk('local')->delete($loan->pdf_path);
        }

        $loan->delete();

        return response()->json(['message' => 'Uitgifte verwijderd.']);
    }

    /**
     * Stream één leen-foto (disk local, zelfde als ontvangst).
     */
    public function photo(LaptopLoan $loan, LaptopLoanPhoto $photo): BinaryFileResponse
    {
        abort_unless($photo->laptop_loan_id === $loan->id, 404);
        abort_unless(Storage::disk('local')->exists($photo->path), 404);

        return response()->file(
            Storage::disk('local')->path($photo->path),
            ['Content-Disposition' => 'inline']
        );
    }

    /**
     * Verwijder één foto (rij + bestand).
     */
    public function destroyPhoto(LaptopLoan $loan, LaptopLoanPhoto $photo): JsonResponse
    {
        abort_unless($photo->laptop_loan_id === $loan->id, 404);

        Storage::disk('local')->delete($photo->path);
        $photo->delete();

        return response()->json(['message' => 'Foto verwijderd.']);
    }

    /**
     * Download overeenkomst-PDF.
     */
    public function agreement(LaptopLoan $loan): BinaryFileResponse
    {
        abort_if(! $loan->pdf_path || ! Storage::disk('local')->exists($loan->pdf_path), 404, 'PDF niet gevonden');

        return response()->download(
            Storage::disk('local')->path($loan->pdf_path),
            $loan->loanNumber() . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    private function storePhotos(Request $request, LaptopLoan $loan): void
    {
        if (! $request->hasFile('photos')) {
            return;
        }

        $sort = (int) ($loan->photos()->max('sort_order') ?? -1) + 1;
        foreach ((array) $request->file('photos') as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('leen-huur/' . $loan->id, $name, 'local');
            $loan->photos()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'sort_order' => $sort++,
            ]);
        }
    }

    private function generatePdf(LaptopLoan $loan): void
    {
        $pdf = Pdf::loadView('invoices.laptop-loan', ['loan' => $loan->fresh()]);
        $pdf->setPaper('a4', 'portrait');

        $pdfDir = 'leen-huur-overeenkomsten';
        $pdfFile = $pdfDir . '/' . $loan->loanNumber() . '.pdf';

        Storage::disk('local')->makeDirectory($pdfDir);
        Storage::disk('local')->put($pdfFile, $pdf->output());

        $loan->update(['pdf_path' => $pdfFile]);
    }
}
