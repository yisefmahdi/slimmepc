<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Favorite;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        $query = Favorite::with(['product.category'])
            ->where('user_id', $request->user()->id);

        if ($request->filled('category')) {
            $query->whereHas('product.category', fn ($q) => $q->where('slug', $request->category));
        }

        $favorites = $query->latest()->paginate(12)->withQueryString();

        $products = $favorites->getCollection()
            ->map(fn (Favorite $f) => $f->product)
            ->filter(fn ($p) => $p && $p->status)
            ->values();

        $products = new LengthAwarePaginator(
            $products,
            $favorites->total(),
            $favorites->perPage(),
            $favorites->currentPage(),
            ['path' => $favorites->url(1), 'query' => request()->query()]
        );

        $favoriteIds = $products->pluck('id')->all();
        $categories = Category::where('status', true)->orderBy('sort_order')->get();

        return view('landing.wishlist', compact('c', 'design', 'products', 'favoriteIds', 'categories'));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ], [
            'product_id.required' => 'Product is verplicht.',
            'product_id.exists' => 'Product niet gevonden.',
        ]);

        $userId = $request->user()->id;
        $existing = Favorite::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
        } else {
            Favorite::create(['user_id' => $userId, 'product_id' => $data['product_id']]);
            $status = 'added';
        }

        $count = Favorite::where('user_id', $userId)->count();

        return response()->json([
            'status' => $status,
            'message' => $status === 'added' ? 'Toegevoegd aan favorieten.' : 'Verwijderd uit favorieten.',
            'count' => $count,
        ]);
    }

    public function destroy(Request $request, Favorite $favorite)
    {
        abort_if($favorite->user_id !== $request->user()->id, 403);
        $favorite->delete();

        return response()->json([
            'message' => 'Verwijderd uit favorieten.',
            'count' => Favorite::where('user_id', $request->user()->id)->count(),
        ]);
    }
}
