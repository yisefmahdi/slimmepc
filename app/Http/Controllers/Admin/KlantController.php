<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKlantRequest;
use App\Http\Requests\Admin\UpdateKlantRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KlantController extends Controller
{
    /**
     * Display the customers management page.
     */
    public function index(): View
    {
        return view('admin.klanten.index');
    }

    /**
     * Return customer data (JSON) for the table — search, filter, pagination.
     */
    public function data(Request $request): JsonResponse
    {
        $query = User::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('klantnummer', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->string('role')->trim()->toString()) {
            $query->where('role', $role);
        }

        $perPage = min((int) $request->input('per_page', 10), 50);

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage, [
                'id', 'name', 'email', 'phone', 'klantnummer',
                'street', 'house_number', 'postcode', 'city',
                'role', 'is_blocked', 'created_at', 'email_verified_at',
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
                'users' => User::where('role', 'user')->count(),
                'technicians' => User::where('role', 'technician')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
        ]);
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreKlantRequest $request): JsonResponse
    {
        $data = $request->validated();

        $password = $data['password'] ?? Str::password(10);
        $data['password'] = $password;

        $data['role'] ??= 'user';
        $data['klantnummer'] ??= $this->generateKlantnummer();

        $klant = User::create($data);

        return response()->json([
            'message' => 'Klant succesvol toegevoegd.',
            'klant' => $klant->only([
                'id', 'name', 'email', 'phone', 'klantnummer', 'role',
            ]),
            'generated_password' => ($request->filled('password') ? null : $password),
        ], 201);
    }

    /**
     * Display a single customer (full data).
     */
    public function show(User $klant): JsonResponse
    {
        return response()->json([
            'klant' => $klant->only([
                'id', 'name', 'email', 'phone', 'klantnummer',
                'street', 'house_number', 'postcode', 'city',
                'role', 'is_blocked', 'created_at', 'email_verified_at',
            ]),
        ]);
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateKlantRequest $request, User $klant): JsonResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $klant->update($data);

        return response()->json([
            'message' => 'Klant succesvol bijgewerkt.',
            'klant' => $klant->fresh()->only([
                'id', 'name', 'email', 'phone', 'klantnummer',
                'street', 'house_number', 'postcode', 'city',
                'role', 'is_blocked',
            ]),
        ]);
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(User $klant): JsonResponse
    {
        if ($klant->id === Auth::id()) {
            return response()->json([
                'message' => 'Je kunt je eigen account niet verwijderen.',
            ], 422);
        }

        $klant->delete();

        return response()->json([
            'message' => 'Klant succesvol verwijderd.',
        ]);
    }

    /**
     * Toggle the blocked status of a customer.
     */
    public function toggleBlock(User $klant): JsonResponse
    {
        if ($klant->id === Auth::id()) {
            return response()->json([
                'message' => 'Je kunt je eigen account niet blokkeren.',
            ], 422);
        }

        if ($klant->isAdmin()) {
            return response()->json([
                'message' => 'Beheerders kunnen niet worden geblokkeerd.',
            ], 422);
        }

        $klant->update(['is_blocked' => ! $klant->is_blocked]);

        return response()->json([
            'message' => $klant->is_blocked
                ? 'Klant is geblokkeerd.'
                : 'Klant is gedeblokkeerd.',
            'is_blocked' => $klant->is_blocked,
        ]);
    }

    /**
     * Change the role of a customer (user / technician / admin).
     */
    public function updateRole(Request $request, User $klant): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'in:user,technician,admin'],
        ]);

        if ($klant->id === Auth::id()) {
            return response()->json([
                'message' => 'Je kunt je eigen rol niet wijzigen.',
            ], 422);
        }

        $klant->update(['role' => $request->string('role')->toString()]);

        return response()->json([
            'message' => 'Rol succesvol gewijzigd naar '
                . ucfirst($klant->role)
                . '.',
            'role' => $klant->role,
        ]);
    }

    /**
     * Generate a unique customer number (KL-YY-XXXX).
     */
    private function generateKlantnummer(): string
    {
        $base = 'KL-'.date('y').'-';
        $number = (int) User::where('klantnummer', 'like', $base.'%')
            ->count() + 1;

        do {
            $candidate = $base.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
            $number++;
        } while (User::where('klantnummer', $candidate)->exists());

        return $candidate;
    }
}

