<?php

use App\Models\Category;
use App\Models\Product;

function makeSearchProduct(string $title, string $brand = 'Testmerk'): Product
{
    $category = Category::firstOrCreate(
        ['slug' => 'zoekcat'],
        ['name' => 'Zoekcat', 'status' => true, 'sort_order' => 0]
    );

    return Product::create([
        'category_id' => $category->id,
        'title' => $title,
        'brand' => $brand,
        'price' => 199.00,
        'stock_status' => 'in_stock',
        'status' => true,
        'description' => 'Test',
    ]);
}

it('searches products across all categories', function () {
    makeSearchProduct('MSI Gaming Laptop XYZ');
    makeSearchProduct('HP Kantoor PC');

    $this->get('/zoeken?q=MSI')
        ->assertOk()
        ->assertSee('Zoekresultaten')
        ->assertSee('MSI Gaming Laptop XYZ')
        ->assertDontSee('HP Kantoor PC');
});

it('shows an empty search state without errors', function () {
    makeSearchProduct('MSI Gaming Laptop XYZ');

    $this->get('/zoeken?q=onbestaandproduct123')
        ->assertOk()
        ->assertSee('Zoekresultaten')
        ->assertDontSee('MSI Gaming Laptop XYZ');
});

it('shows all products when the query is empty', function () {
    makeSearchProduct('MSI Gaming Laptop XYZ');

    $this->get('/zoeken')
        ->assertOk()
        ->assertSee('MSI Gaming Laptop XYZ');
});
