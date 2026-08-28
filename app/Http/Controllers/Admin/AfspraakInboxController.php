<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AfspraakSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AfspraakInboxController extends Controller
{
    public function index()
    {
        $submissions = AfspraakSubmission::orderByDesc('created_at')
            ->paginate(15);

        $newCount = AfspraakSubmission::new()->count();

        return view('admin.afspraak-aanvragen.index', compact('submissions', 'newCount'));
    }

    public function data(Request $request)
    {
        $query = AfspraakSubmission::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('device', 'like', "%{$search}%")
                    ->orWhere('afspraak_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'new') {
                $query->new();
            } else {
                $query->where('status', $status);
            }
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $submissions = $query->orderByDesc('created_at')->paginate($perPage);

        $statusLabels = [
            'new'         => 'Nieuw',
            'in_progress' => 'In behandeling',
            'completed'   => 'Afgerond',
        ];

        $rows = $submissions->getCollection()->map(function ($s) use ($statusLabels) {
            return [
                'id'              => $s->id,
                'afspraak_number'=> $s->afspraak_number,
                'name'            => $s->name,
                'email'           => $s->email,
                'phone'           => $s->phone,
                'device'          => $s->device,
                'preferred_date'  => $s->preferred_date ? $s->preferred_date->format('d-m-Y') : '-',
                'preferred_time'  => $s->preferred_time,
                'status'          => $s->status,
                'status_label'    => $statusLabels[$s->status] ?? $s->status,
                'is_new'          => $s->status === 'new',
                'created_at'      => $s->created_at->format('d-m-Y H:i'),
            ];
        });

        return response()->json([
            'data' => $rows,
            'counts' => [
                'new'  => AfspraakSubmission::new()->count(),
                'total' => AfspraakSubmission::count(),
            ],
            'pagination' => [
                'current_page' => $submissions->currentPage(),
                'last_page'    => $submissions->lastPage(),
                'per_page'     => $submissions->perPage(),
                'total'        => $submissions->total(),
                'from'         => $submissions->firstItem(),
                'to'           => $submissions->lastItem(),
            ],
        ]);
    }

    public function newCount()
    {
        return response()->json([
            'count' => AfspraakSubmission::new()->count(),
        ]);
    }

    public function show(AfspraakSubmission $afspraakSubmission)
    {
        return response()->json([
            'id'              => $afspraakSubmission->id,
            'afspraak_number'=> $afspraakSubmission->afspraak_number,
            'name'            => $afspraakSubmission->name,
            'email'           => $afspraakSubmission->email,
            'phone'           => $afspraakSubmission->phone,
            'street'          => $afspraakSubmission->street,
            'house_number'    => $afspraakSubmission->house_number,
            'postcode'        => $afspraakSubmission->postcode,
            'city'            => $afspraakSubmission->city,
            'device'          => $afspraakSubmission->device,
            'problem'         => $afspraakSubmission->problem,
            'preferred_date'  => $afspraakSubmission->preferred_date ? $afspraakSubmission->preferred_date->format('d-m-Y') : null,
            'preferred_time'  => $afspraakSubmission->preferred_time,
            'status'          => $afspraakSubmission->status,
            'created_at'      => $afspraakSubmission->created_at->format('d-m-Y H:i'),
        ]);
    }

    public function status(Request $request, AfspraakSubmission $afspraakSubmission)
    {
        $request->validate([
            'status' => ['required', 'in:new,in_progress,completed'],
        ]);

        $afspraakSubmission->update(['status' => $request->input('status')]);

        return response()->json([
            'success'  => true,
            'status'   => $afspraakSubmission->status,
            'newCount' => AfspraakSubmission::new()->count(),
        ]);
    }

    public function destroy(AfspraakSubmission $afspraakSubmission)
    {
        $afspraakSubmission->delete();

        return response()->json([
            'success' => true,
            'newCount' => AfspraakSubmission::new()->count(),
        ]);
    }
}
