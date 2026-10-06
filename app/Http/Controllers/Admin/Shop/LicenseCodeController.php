<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\LicenseCode;
use App\Models\Product;
use Illuminate\Http\Request;

class LicenseCodeController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('is_digital', true)->orderBy('title')->get();
        $allProducts = Product::orderBy('title')->get(['id', 'title', 'is_digital']);

        return view('admin.shop.license_codes.index', compact('products', 'allProducts'));
    }

    public function data(Request $request)
    {
        $query = LicenseCode::query()->with(['product', 'order']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($qq) => $qq->where('title', 'like', "%{$search}%"))
                  ->orWhereHas('order', fn ($qq) => $qq->where('order_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('product_id') && $request->product_id !== 'all') {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $query->orderBy('id', 'desc');

        $perPage = (int) $request->integer('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;

        $codes = $query->paginate($perPage)->withQueryString();

        $counts = [
            'total' => LicenseCode::count(),
            'available' => LicenseCode::where('status', 'available')->count(),
            'sold' => LicenseCode::where('status', 'sold')->count(),
        ];

        return response()->json([
            'codes' => $codes,
            'counts' => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'code' => 'required|string|max:255|unique:license_codes,code',
        ], [
            'product_id.required' => 'Kies een product.',
            'code.required' => 'Vul een licentiecode in.',
            'code.unique' => 'Deze code bestaat al.',
        ]);

        $code = LicenseCode::create([
            'product_id' => $data['product_id'],
            'code' => trim($data['code']),
            'status' => 'available',
        ]);

        $code->load(['product', 'order']);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Licentiecode succesvol toegevoegd.',
                'code' => $code,
            ], 201);
        }

        return redirect()
            ->route('admin.webshop.license-codes.index')
            ->with('success', 'Licentiecode succesvol toegevoegd.');
    }

    public function destroy(LicenseCode $licenseCode)
    {
        $licenseCode->delete();

        return response()->json([
            'message' => 'Licentiecode verwijderd.',
        ]);
    }
}
