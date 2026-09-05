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

        // Dynamic features filter: ?Processor=Intel%20Core%20i5&RAM=16GB etc. (grouped by title)
        $reserved = ['brand','price','price_min','price_max','sort','per_page','page','q'];
        foreach ($request->query() as $key => $val) {
            if (in_array(strtolower($key), $reserved)) continue;
            if ($val === null || $val === '') continue;
            $values = is_array($val) ? $val : explode(',', $val);
            $values = array_filter(array_map('trim', $values));
            if (empty($values)) continue;
            $query->where(function($q) use ($key, $values) {
                foreach ($values as $v) {
                    $q->orWhere(function($sub) use ($key, $v) {
                        $sub->whereRaw("JSON_CONTAINS(features, JSON_OBJECT('title', ?, 'value', ?))", [$key, $v])
                            ->orWhere(function($sub2) use ($key, $v) {
                                $sub2->whereRaw("LOWER(features) LIKE ?", ['%"title":"'.strtolower($key).'"%'])
                                     ->whereRaw("LOWER(features) LIKE ?", ['%"value":"'.strtolower($v).'"%']);
                            });
                    });
                }
            });
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

        // Dynamic filterGroups from features: group by title, collect distinct values, count, show first 10
        $allFeaturesRaw = Product::where('status', true)->where('category_id', $currentCategory->id)->pluck('features');
        $groupMap = [];
        foreach ($allFeaturesRaw as $features) {
            if (!is_array($features)) continue;
            foreach ($features as $f) {
                if (!is_array($f) || !isset($f['title'], $f['value'])) continue;
                $title = trim((string)($f['title'] ?? ''));
                $value = trim((string)($f['value'] ?? ''));
                if ($title === '' || $value === '') continue;
                $normTitle = strtolower($title);
                $normValue = strtolower($value);
                if (!isset($groupMap[$normTitle])) {
                    $groupMap[$normTitle] = ['displayTitle' => $title, 'values' => []];
                }
                // Keep first display case, but ensure we don't duplicate
                if (!isset($groupMap[$normTitle]['values'][$normValue])) {
                    $groupMap[$normTitle]['values'][$normValue] = ['display' => $value, 'raw' => $value, 'count' => 0];
                }
            }
        }
        // Calculate counts for each distinct value
        $filterGroups = [];
        foreach ($groupMap as $normTitle => $group) {
            $displayTitle = $group['displayTitle'];
            $values = [];
            foreach ($group['values'] as $normValue => $v) {
                $rawValue = $v['raw'];
                $display = $v['display'];
                // Count products in this category with this feature
                $count = Product::where('status', true)
                    ->where('category_id', $currentCategory->id)
                    ->where(function($q) use ($displayTitle, $rawValue) {
                        $q->whereRaw("JSON_CONTAINS(features, JSON_OBJECT('title', ?, 'value', ?))", [$displayTitle, $rawValue])
                          ->orWhere(function($sub) use ($displayTitle, $rawValue) {
                              $sub->whereRaw("LOWER(features) LIKE ?", ['%"title":"'.strtolower($displayTitle).'"%'])
                                   ->whereRaw("LOWER(features) LIKE ?", ['%"value":"'.strtolower($rawValue).'"%']);
                          });
                    })->count();
                // Also try with normalized title variants (lower, ucfirst) for robustness
                if ($count === 0) {
                    $count = Product::where('status', true)
                        ->where('category_id', $currentCategory->id)
                        ->whereRaw("LOWER(features) LIKE ? AND LOWER(features) LIKE ?", ['%"title":"'.strtolower($displayTitle).'"%', '%"value":"'.strtolower($rawValue).'"%'])
                        ->count();
                }
                $values[] = ['display' => $display, 'raw' => $rawValue, 'count' => $count];
            }
            // Sort by count desc then display asc
            usort($values, fn($a,$b) => $b['count'] <=> $a['count'] ?: strcmp($a['display'], $b['display']));
            // Only include groups that have at least one value with count>0
            $values = array_values(array_filter($values, fn($v) => $v['count'] > 0));
            if (!empty($values)) {
                $filterGroups[] = ['title' => $displayTitle, 'slug' => strtolower($displayTitle), 'values' => $values];
            }
        }
        // Sort groups by title
        usort($filterGroups, fn($a,$b) => strcmp($a['title'], $b['title']));

        return view('landing.webshop', compact('c', 'design', 'allCategories', 'currentCategory', 'products', 'availableBrands', 'sort', 'filterGroups'));
    }

    public function show(string $categorySlug, string $productSlug)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        $category = Category::where('slug', $categorySlug)
            ->where('status', true)
            ->firstOrFail();

        $product = Product::where('slug', $productSlug)
            ->where('status', true)
            ->where('category_id', $category->id)
            ->with('category')
            ->firstOrFail();

        // All active categories for header chips
        $allCategories = Category::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Related / accessories — same category, exclude current
        $relatedProducts = Product::where('status', true)
            ->where('category_id', $category->id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('landing.product-details', compact('c', 'design', 'category', 'product', 'allCategories', 'relatedProducts'));
    }
}
