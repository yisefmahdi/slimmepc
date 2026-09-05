<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.shop.coupons.index');
    }

    public function data(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status === '1' ? 1 : 0);
        }

        $query->orderByDesc('id');

        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $coupons = $query->paginate($perPage)->withQueryString();

        $counts = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 1)->count(),
            'inactive' => Coupon::where('status', 0)->count(),
        ];

        return response()->json([
            'coupons' => $coupons,
            'counts' => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01|max:999999',
            'min_amount' => 'nullable|numeric|min:0|max:999999',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'usage_limit' => 'nullable|integer|min:1|max:100000',
            'is_single_use' => 'nullable|boolean',
        ]);

        $data['code'] = Str::upper(trim($data['code']));
        $data['is_single_use'] = (bool) ($data['is_single_use'] ?? false);

        if ($data['discount_type'] === 'percentage' && (float) $data['discount_value'] > 100) {
            return response()->json(['message' => 'Percentage kan niet hoger zijn dan 100.', 'errors' => ['discount_value' => ['Percentage kan niet hoger zijn dan 100.']]], 422);
        }

        // datetime-local comes as local Amsterdam time (no timezone) — convert to UTC for storage
        if (!empty($data['start_date'])) {
            $data['start_date'] = \Carbon\Carbon::parse($data['start_date'], 'Europe/Amsterdam')->utc();
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = \Carbon\Carbon::parse($data['end_date'], 'Europe/Amsterdam')->utc();
        }

        $coupon = Coupon::create($data);

        return response()->json(['message' => 'Kortingscode aangemaakt.', 'coupon' => $coupon], 201);
    }

    public function show(Coupon $coupon)
    {
        return response()->json(['coupon' => $coupon]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01|max:999999',
            'min_amount' => 'nullable|numeric|min:0|max:999999',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'usage_limit' => 'nullable|integer|min:1|max:100000',
            'is_single_use' => 'nullable|boolean',
        ]);

        $data['code'] = Str::upper(trim($data['code']));
        $data['is_single_use'] = (bool) ($data['is_single_use'] ?? false);

        if ($data['discount_type'] === 'percentage' && (float) $data['discount_value'] > 100) {
            return response()->json(['message' => 'Percentage kan niet hoger zijn dan 100.', 'errors' => ['discount_value' => ['Percentage kan niet hoger zijn dan 100.']]], 422);
        }

        if (!empty($data['start_date'])) {
            $data['start_date'] = \Carbon\Carbon::parse($data['start_date'], 'Europe/Amsterdam')->utc();
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = \Carbon\Carbon::parse($data['end_date'], 'Europe/Amsterdam')->utc();
        }

        $coupon->update($data);

        return response()->json(['message' => 'Kortingscode bijgewerkt.', 'coupon' => $coupon->fresh()]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(['message' => 'Kortingscode verwijderd.']);
    }

    public function toggleStatus(Request $request, Coupon $coupon)
    {
        $request->validate(['status' => 'required|boolean']);
        $coupon->update(['status' => (bool) $request->status]);
        return response()->json(['message' => 'Status bijgewerkt.', 'status' => $coupon->status]);
    }
}
