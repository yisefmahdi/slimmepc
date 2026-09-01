<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $brands = Product::select('brand')->whereNotNull('brand')->distinct()->pluck('brand');

        return view('admin.shop.products.index', compact('categories', 'brands'));
    }

    public function data(Request $request)
    {
        $query = Product::query()->with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand') && $request->brand !== 'all') {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status === '1' ? 1 : 0);
        }

        if ($request->filled('stock_status') && $request->stock_status !== 'all') {
            $query->where('stock_status', $request->stock_status);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $query->orderBy('id', 'desc');

        $perPage = (int) $request->integer('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;

        $products = $query->paginate($perPage)->withQueryString();

        $counts = [
            'total' => Product::count(),
            'active' => Product::where('status', 1)->count(),
            'inactive' => Product::where('status', 0)->count(),
            'in_stock' => Product::where('stock_status', 'in_stock')->count(),
            'featured' => Product::where('is_featured', 1)->count(),
        ];

        return response()->json([
            'products' => $products,
            'counts' => $counts,
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.shop.products.create', compact('categories'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $product->load('category');

        return view('admin.shop.products.edit', compact('product', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255|unique:products,title',
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:64|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'status' => 'required|boolean',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
            'external_link' => 'nullable|url',
            'delivery_time' => 'nullable|string|max:255',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
            'download_32bit_url' => 'nullable|url',
            'download_64bit_url' => 'nullable|url',
            'manual_url' => 'nullable|url',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');

        // Clean arrays
        $data['features'] = array_values(array_filter($request->input('features', [])));
        $data['colors'] = array_values(array_filter($request->input('colors', [])));
        $data['sizes'] = array_values(array_filter($request->input('sizes', [])));

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products/main', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            $paths = [];
            foreach ($request->file('gallery_images') as $file) {
                $paths[] = $file->store('products/gallery', 'public');
            }
            $data['gallery_images'] = array_slice($paths, 0, 10);
        }

        $product = Product::create($data);
        $product->load('category');
        Cms::bust();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Product succesvol aangemaakt.',
                'product' => $product,
            ], 201);
        }

        return redirect()
            ->route('admin.webshop.products.index')
            ->with('success', 'Product succesvol aangemaakt.');
    }

    public function show(Product $product)
    {
        $product->load('category');

        return response()->json([
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255|unique:products,title,' . $product->id,
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:64|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'status' => 'required|boolean',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string',
            'remove_main_image' => 'nullable|boolean',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
            'existing_gallery' => 'nullable|array',
            'existing_gallery.*' => 'nullable|string',
            'external_link' => 'nullable|url',
            'delivery_time' => 'nullable|string|max:255',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
            'download_32bit_url' => 'nullable|url',
            'download_64bit_url' => 'nullable|url',
            'manual_url' => 'nullable|url',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['features'] = array_values(array_filter($request->input('features', [])));
        $data['colors'] = array_values(array_filter($request->input('colors', [])));
        $data['sizes'] = array_values(array_filter($request->input('sizes', [])));

        if ($request->boolean('remove_main_image')) {
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $data['main_image'] = null;
        } elseif ($request->hasFile('main_image')) {
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('products/main', 'public');
        } else {
            unset($data['main_image']);
        }

        // Gallery management: Selective deletion and merging
        $keep = $request->input('existing_gallery', []);
        $currentGallery = (array) $product->gallery_images;
        
        $toDelete = array_diff($currentGallery, $keep);
        foreach ($toDelete as $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }
        $newPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $newPaths[] = $file->store('products/gallery', 'public');
            }
        }
        
        $finalGallery = array_merge($keep, $newPaths);
        // Limit total images to 10
        $data['gallery_images'] = array_slice($finalGallery, 0, 10);

        $product->update($data);
        $product->load('category');
        Cms::bust();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Product succesvol bijgewerkt.',
                'product' => $product,
            ]);
        }

        return redirect()
            ->route('admin.webshop.products.index')
            ->with('success', 'Product succesvol bijgewerkt.');
    }

    public function destroy(Product $product)
    {
        if ($product->main_image) {
            Storage::disk('public')->delete($product->main_image);
        }

        if ($product->gallery_images) {
            foreach ((array) $product->gallery_images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $product->delete();
        Cms::bust();

        return response()->json([
            'message' => 'Product succesvol verwijderd.',
        ]);
    }

    public function toggleStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $product->update(['status' => (bool) $request->status]);
        Cms::bust();

        return response()->json([
            'message' => 'Status bijgewerkt.',
            'status' => $product->status,
        ]);
    }

    public function toggleFeatured(Request $request, Product $product)
    {
        $request->validate([
            'is_featured' => 'required|boolean',
        ]);

        $product->update(['is_featured' => (bool) $request->is_featured]);
        Cms::bust();

        return response()->json([
            'message' => 'Home status bijgewerkt.',
            'is_featured' => $product->is_featured,
        ]);
    }
}
