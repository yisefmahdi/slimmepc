<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatAvailability;
use App\Models\ChatClosedDate;
use App\Services\Chat\ChatAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(ChatAvailabilityService $service): View
    {
        return view('admin.chat.availability.index', [
            'days' => ChatAvailability::orderBy('day_of_week')->get(),
            'closedDates' => ChatClosedDate::orderBy('closed_at')->get(),
            'status' => $service->status(),
        ]);
    }

    public function updateDay(Request $request, ChatAvailability $availability): JsonResponse
    {
        $validated = $request->validate([
            'is_open' => ['required', 'boolean'],
            'open_at' => ['nullable', 'date_format:H:i'],
            'close_at' => ['nullable', 'date_format:H:i', 'after:open_at'],
        ], [
            'close_at.after' => 'Sluitingstijd moet na openingstijd liggen.',
        ]);

        $availability->update([
            'is_open' => $validated['is_open'],
            'open_at' => $validated['is_open'] ? ($validated['open_at'] ?? '09:00') : null,
            'close_at' => $validated['is_open'] ? ($validated['close_at'] ?? '17:00') : null,
        ]);

        return response()->json(['message' => 'Openingstijd bijgewerkt.']);
    }

    public function storeDate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'closed_at' => ['required', 'date', 'after_or_equal:today', 'unique:chat_closed_dates,closed_at'],
            'reason' => ['nullable', 'string', 'max:255'],
        ], [
            'closed_at.required' => 'Kies een datum.',
            'closed_at.unique' => 'Deze datum staat er al in.',
        ]);

        $date = ChatClosedDate::create($validated);

        return response()->json(['message' => 'Vrije dag toegevoegd.', 'date' => $date], 201);
    }

    public function destroyDate(ChatClosedDate $closedDate): JsonResponse
    {
        $closedDate->delete();

        return response()->json(['message' => 'Vrije dag verwijderd.']);
    }
}
