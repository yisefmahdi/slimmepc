<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.shop.categories.index');
    }

    public function data(Request $request)
    {
        $query = Category::query()->withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('icon', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status === '1' ? 1 : 0);
        }

        $query->orderBy('sort_order')->orderBy('id', 'desc');

        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $categories = $query->paginate($perPage)->withQueryString();

        $counts = [
            'total' => Category::count(),
            'active' => Category::where('status', 1)->count(),
            'inactive' => Category::where('status', 0)->count(),
        ];

        return response()->json([
            'categories' => $categories,
            'counts' => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);

        Cache::forget('webshop.header.categories');
        Cms::bust();

        return response()->json([
            'message' => 'Categorie succesvol aangemaakt.',
            'category' => $category,
        ], 201);
    }

    public function show(Category $category)
    {
        $category->loadCount('products');

        return response()->json([
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        // Prevent deactivating if has products
        if ((int) $data['status'] === 0 && $category->products()->exists()) {
            return response()->json([
                'message' => 'Kan categorie niet deactiveren: er zijn producten gekoppeld.',
            ], 422);
        }

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        Cache::forget('webshop.header.categories');
        Cms::bust();

        return response()->json([
            'message' => 'Categorie succesvol bijgewerkt.',
            'category' => $category->fresh()->loadCount('products'),
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Kan categorie niet verwijderen: er zijn producten gekoppeld.',
            ], 422);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        Cache::forget('webshop.header.categories');
        Cms::bust();

        return response()->json([
            'message' => 'Categorie succesvol verwijderd.',
        ]);
    }

    public function toggleStatus(Request $request, Category $category)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        if ((int) $request->status === 0 && $category->products()->exists()) {
            return response()->json([
                'message' => 'Kan categorie niet deactiveren: er zijn producten gekoppeld.',
            ], 422);
        }

        $category->update(['status' => (bool) $request->status]);

        Cache::forget('webshop.header.categories');
        Cms::bust();

        return response()->json([
            'message' => 'Status bijgewerkt.',
            'status' => $category->status,
        ]);
    }
}
