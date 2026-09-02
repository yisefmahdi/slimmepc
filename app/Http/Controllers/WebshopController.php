<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\Cms;
use Illuminate\Http\Request;

class WebshopController extends Controller
{
    public function index(Request $request, string $slug)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        // All active categories for chips + header (ordered)
        $allCategories = Category::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $currentCategory = Category::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        // Base products query — only products of this category
        $query = Product::where('status', true)->with('category')
            ->where('category_id', $currentCategory->id);

        // Brand filter ?brand=HP,Lenovo
        $brandFilter = $request->query('brand');
        if ($brandFilter) {
            $brands = is_array($brandFilter) ? $brandFilter : explode(',', $brandFilter);
            $brands = array_filter(array_map('trim', $brands));
            if (!empty($brands)) {
                $query->whereIn('brand', $brands);
            }
        }

        // Price filter ?price_min=0&price_max=2000
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        if (is_numeric($priceMin)) {
            $query->where('price', '>=', (float) $priceMin);
        }
        if (is_numeric($priceMax)) {
            $query->where('price', '<=', (float) $priceMax);
        }
        // Single range slider ?price=1500
        if ($request->filled('price') && !$request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->query('price'));
        }

        // Sorting ?sort=populair|prijs_asc|prijs_desc|nieuwste
        $sort = $request->query('sort', 'populair');
        match ($sort) {
            'prijs_asc', 'price_asc' => $query->orderBy('price', 'asc'),
            'prijs_desc', 'price_desc' => $query->orderBy('price', 'desc'),
            'nieuwste', 'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        // Pagination ?per_page=12
        $perPage = (int) $request->query('per_page', 12);
        $perPage = in_array($perPage, [12, 24, 48]) ? $perPage : 12;

        $products = $query->paginate($perPage)->withQueryString();

        // Distinct brands for filter sidebar (from active products)
        $availableBrands = Product::where('status', true)
            ->when($currentCategory, fn($q) => $q->where('category_id', $currentCategory->id))
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->map(fn($b) => [
                'name' => $b,
                'count' => Product::where('status', true)->where('brand', $b)->when($currentCategory, fn($q) => $q->where('category_id', $currentCategory->id))->count(),
            ]);

        return view('landing.webshop', compact('c', 'design', 'allCategories', 'currentCategory', 'products', 'availableBrands', 'sort'));
    }
}
