<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingRateController extends Controller
{
    public function index(): View
    {
        return view('admin.shipping.index');
    }

    public function data(): JsonResponse
    {
        return response()->json([
            'data' => ShippingRate::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:shipping_rates,slug',
            'price' => 'required|numeric|min:0|max:9999',
            'free_above' => 'nullable|numeric|min:0|max:999999',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [], ['name' => 'naam']);

        $rate = ShippingRate::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'price' => $data['price'],
            'free_above' => $data['free_above'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return response()->json(['message' => 'Verzendoptie toegevoegd.', 'rate' => $rate], 201);
    }

    public function update(Request $request, ShippingRate $shipping): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:9999',
            'free_above' => 'nullable|numeric|min:0|max:999999',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $shipping->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'free_above' => $data['free_above'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? $shipping->is_active),
            'sort_order' => $data['sort_order'] ?? $shipping->sort_order,
        ]);

        return response()->json(['message' => 'Verzendoptie bijgewerkt.', 'rate' => $shipping->fresh()]);
    }

    public function destroy(ShippingRate $shipping): JsonResponse
    {
        if (in_array($shipping->slug, ['delivery', 'pickup'], true)) {
            return response()->json(['message' => 'Standaardopties kunnen niet worden verwijderd, wel uitgeschakeld.'], 422);
        }
        $shipping->delete();

        return response()->json(['message' => 'Verzendoptie verwijderd.']);
    }

    public function toggle(ShippingRate $shipping): JsonResponse
    {
        $shipping->update(['is_active' => ! $shipping->is_active]);

        return response()->json(['message' => 'Status bijgewerkt.', 'is_active' => $shipping->is_active]);
    }
}
