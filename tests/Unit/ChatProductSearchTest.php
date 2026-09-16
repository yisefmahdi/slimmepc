<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Services\Ai\Features\ChatTools\ProductDetailsTool;
use App\Services\Chat\ChatProductSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChatProductSearchTest extends TestCase
{
    use RefreshDatabase;

    private function makeCat(string $name, string $slug): Category
    {
        return Category::create(['name' => $name, 'slug' => $slug, 'status' => true, 'sort_order' => 0]);
    }

    private function makeProduct(Category $cat, string $title, float $price, string $desc = ''): Product
    {
        return Product::create([
            'category_id' => $cat->id, 'title' => $title, 'slug' => Str::slug($title).'-'.Str::random(6),
            'brand' => 'Test', 'price' => $price, 'stock_status' => 'in_stock', 'status' => true,
            'description' => $desc,
        ]);
    }

    public function test_plural_query_finds_products_via_singular_fallback(): void
    {
        $cat = $this->makeCat('Laptops', 'laptops');
        $p = $this->makeProduct($cat, 'Test Laptop 15', 599, 'Betrouwbare laptop voor dagelijks gebruik.');

        $result = (new ChatProductSearch)->search('laptops', 6);

        expect($result['products'])->not->toBeEmpty()
            ->and(collect($result['products'])->pluck('id')->all())->toContain($p->id);
    }

    public function test_category_slug_hard_filters_wrong_types(): void
    {
        $lap = $this->makeCat('Laptops', 'laptops');
        $pc = $this->makeCat('Gaming PC', 'gaming-pc');
        $laptop = $this->makeProduct($lap, 'Office Laptop', 499, 'Laptop voor kantoor.');
        $this->makeProduct($pc, 'Gaming Desktop', 999, 'Krachtige gaming desktop computer.');

        $result = (new ChatProductSearch)->search('gaming laptop', 6, null, 'laptops');

        $ids = collect($result['products'])->pluck('id')->all();
        expect($ids)->toContain($laptop->id);
        foreach ($result['products'] as $card) {
            expect($card['category_slug'])->toBe('laptops');
        }
    }

    public function test_empty_category_falls_back_to_whole_category(): void
    {
        $cat = $this->makeCat('Laptops', 'laptops');
        $p = $this->makeProduct($cat, 'Zenbook Pro', 799, 'Dunne en lichte machine.');

        // 'koelkast' matcht niets — met categorie-filter valt hij terug
        // op het categorie-aanbod in plaats van "geen laptops".
        $result = (new ChatProductSearch)->search('koelkast', 6, null, 'laptops');

        expect(collect($result['products'])->pluck('id')->all())->toContain($p->id);
    }

    public function test_truly_empty_search_reports_none(): void
    {
        $result = (new ChatProductSearch)->search('koelkast', 6);

        expect($result['products'])->toBeEmpty();
    }

    public function test_unknown_category_slug_is_ignored_safely(): void
    {
        $cat = $this->makeCat('Laptops', 'laptops');
        $p = $this->makeProduct($cat, 'Test Laptop 15', 599, 'Betrouwbare laptop.');

        $result = (new ChatProductSearch)->search('laptop', 6, null, 'bestaat-niet');

        expect(collect($result['products'])->pluck('id')->all())->toContain($p->id);
    }

    public function test_product_details_resolves_by_id_slug_and_url(): void
    {
        $cat = $this->makeCat('Laptops', 'laptops');
        $p = $this->makeProduct($cat, 'Detail Laptop', 499, 'Mooie laptop.');

        $byId = (new ProductDetailsTool)->execute(['product_id' => $p->id]);
        $bySlug = (new ProductDetailsTool)->execute(['slug' => $p->slug]);
        $byUrl = (new ProductDetailsTool)->execute(['url' => 'http://test/webshop/laptops/'.$p->slug]);

        foreach ([$byId, $bySlug, $byUrl] as $out) {
            expect($out)->toContain('Detail Laptop');
        }
    }
}
