<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepairSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RepairInboxController extends Controller
{
    /**
     * Display the repair submissions page.
     */
    public function index(): View
    {
        return view('admin.reparatie-aanmeldingen.index');
    }

    /**
     * Return repair submission data (JSON) — search, filter, pagination.
     */
    public function data(Request $request): JsonResponse
    {
        $query = RepairSubmission::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('device', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        $perPage = min((int) $request->input('per_page', 10), 50);

        $paginator = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage, [
                'id', 'repair_number', 'name', 'email', 'phone', 'device',
                'brand', 'model', 'status', 'created_at',
            ]);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'current' => $paginator->currentPage(),
                'last' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
            'counts' => [
                'new' => RepairSubmission::where('status', 'new')->count(),
                'total' => RepairSubmission::count(),
            ],
        ]);
    }

    /**
     * Return a single submission with its photos.
     */
    public function show(RepairSubmission $repairSubmission): JsonResponse
    {
        $photos = collect($repairSubmission->photos ?? [])->map(function ($file) use ($repairSubmission) {
            return [
                'file' => $file,
                'url' => route('admin.reparatie-aanmeldingen.photo', [
                    'repairSubmission' => $repairSubmission->id,
                    'file' => $file,
                ]),
            ];
        });

        return response()->json([
            'submission' => $repairSubmission->only([
                'id', 'repair_number', 'device', 'problems', 'description',
                'brand', 'model', 'serial', 'data_importance', 'opened_before',
                'name', 'email', 'phone', 'postcode', 'delivery_method', 'contact_preference',
                'privacy', 'status', 'ip_address', 'created_at',
            ]),
            'photos' => $photos,
        ]);
    }

    /**
     * Update the status of a submission.
     */
    public function status(Request $request, RepairSubmission $repairSubmission): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:new,in_progress,completed'],
        ]);

        $repairSubmission->update(['status' => $request->string('status')->toString()]);

        return response()->json([
            'message' => 'Status bijgewerkt.',
            'status' => $repairSubmission->status,
        ]);
    }

    /**
     * Delete a submission (photos removed from disk).
     */
    public function destroy(RepairSubmission $repairSubmission): JsonResponse
    {
        Storage::disk('local')->deleteDirectory('repair/'.$repairSubmission->id);

        $repairSubmission->delete();

        return response()->json([
            'message' => 'Aanvraag succesvol verwijderd.',
        ]);
    }

    /**
     * Stream a stored photo for display in the admin detail pane.
     */
    public function photo(RepairSubmission $repairSubmission, string $file): BinaryFileResponse
    {
        $path = 'repair/'.$repairSubmission->id.'/'.$file;

        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(
            Storage::disk('local')->path($path),
            ['Content-Disposition' => 'inline']
        );
    }

    /**
     * Current number of new submissions — used for the sidebar badge.
     */
    public function newCount(): JsonResponse
    {
        return response()->json([
            'count' => RepairSubmission::where('status', 'new')->count(),
        ]);
    }
}
