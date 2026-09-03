<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function data(Request $request)
    {
        $query = ProductReview::with(['product:id,title,slug,category_id', 'product.category:id,name,slug', 'user:id,name']);

        if ($request->filled('product_id') && $request->product_id !== 'all') {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_approved', $request->status === 'approved' ? 1 : 0);
        }
        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', (int) $request->rating);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('body', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%")
                  ->orWhere('guest_name', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('product', fn($pq) => $pq->where('title', 'like', "%{$s}%"));
            });
        }

        $query->latest();

        $perPage = (int) $request->integer('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;
        $reviews = $query->paginate($perPage)->withQueryString();

        $counts = [
            'total' => ProductReview::count(),
            'pending' => ProductReview::where('is_approved', false)->count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
        ];

        return response()->json(['reviews' => $reviews, 'counts' => $counts]);
    }

    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);
        $review->product->recalcRating();
        return response()->json(['message' => 'Review goedgekeurd.', 'review' => $review]);
    }

    public function reject(ProductReview $review)
    {
        $wasApproved = $review->is_approved;
        $review->update(['is_approved' => false]);
        if ($wasApproved) $review->product->recalcRating();
        return response()->json(['message' => 'Review afgekeurd.', 'review' => $review]);
    }

    public function destroy(ProductReview $review)
    {
        $product = $review->product;
        $wasApproved = $review->is_approved;
        $review->delete();
        if ($wasApproved) $product->recalcRating();
        return response()->json(['message' => 'Review verwijderd.']);
    }

    public function productReviews(Product $product)
    {
        $reviews = $product->reviews()->with('user:id,name')->latest()->paginate(10);
        return response()->json(['reviews' => $reviews, 'avg' => $product->rating_avg, 'count' => $product->rating_count]);
    }
}
