<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class WebshopReviewController extends Controller
{
    public function store(Request $request, string $categorySlug, string $productSlug)
    {
        $product = Product::where('slug', $productSlug)
            ->whereHas('category', fn($q) => $q->where('slug', $categorySlug))
            ->where('status', true)
            ->firstOrFail();

        $isGuest = !auth()->check();

        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|min:10|max:1000',
            'title' => 'nullable|string|max:120',
            'guest_name' => ($isGuest ? 'required' : 'required') . '|string|max:80',
        ];

        if ($isGuest) {
            $rules['guest_email'] = 'required|email|max:120';
        } else {
            $rules['guest_email'] = 'nullable|email|max:120';
        }

        $data = $request->validate($rules);

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'guest_name' => $data['guest_name'] ?? null,
            'guest_email' => $isGuest ? ($data['guest_email'] ?? null) : null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'is_approved' => true,
            'ip_address' => $request->ip(),
        ]);

        $product->recalcRating();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Bedankt! Je review is ontvangen en wordt binnenkort beoordeeld.',
                'review' => $review,
            ], 201);
        }

        return back()->with('success', 'Bedankt! Je review is ontvangen en wordt binnenkort beoordeeld.');
    }
}
