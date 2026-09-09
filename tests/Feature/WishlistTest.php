<?php

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;

function makeWishlistProduct(string $title = 'Wishlist Laptop'): Product
{
    $category = Category::create([
        'name' => 'Wishcat',
        'status' => true,
        'sort_order' => 0,
    ]);

    return Product::create([
        'category_id' => $category->id,
        'title' => $title,
        'brand' => 'Wishmerk',
        'price' => 99.00,
        'stock_status' => 'in_stock',
        'status' => true,
        'description' => 'Test',
    ]);
}

it('redirects guests to login for the wishlist page', function () {
    $this->get('/wishlist')->assertRedirect('/login');
});

it('shows an empty wishlist for new users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/wishlist')
        ->assertOk()
        ->assertSee('Verlanglijstje')
        ->assertSee('Nog geen favorieten');
});

it('toggles a favorite on and off', function () {
    $user = User::factory()->create();
    $product = makeWishlistProduct();

    $add = $this->actingAs($user)->postJson('/wishlist/toggle', ['product_id' => $product->id]);
    $add->assertOk()->assertJson(['status' => 'added', 'count' => 1]);
    $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'product_id' => $product->id]);

    $remove = $this->actingAs($user)->postJson('/wishlist/toggle', ['product_id' => $product->id]);
    $remove->assertOk()->assertJson(['status' => 'removed', 'count' => 0]);
    $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'product_id' => $product->id]);
});

it('shows favorited products on the wishlist page', function () {
    $user = User::factory()->create();
    $product = makeWishlistProduct('Mijn Favoriete Laptop');
    Favorite::create(['user_id' => $user->id, 'product_id' => $product->id]);

    $this->actingAs($user)
        ->get('/wishlist')
        ->assertOk()
        ->assertSee('Mijn Favoriete Laptop');
});

it('hides inactive products from the wishlist', function () {
    $user = User::factory()->create();
    $product = makeWishlistProduct('Oude Laptop');
    Favorite::create(['user_id' => $user->id, 'product_id' => $product->id]);
    $product->update(['status' => false]);

    $this->actingAs($user)
        ->get('/wishlist')
        ->assertOk()
        ->assertDontSee('Oude Laptop')
        ->assertSee('Nog geen favorieten');
});

it('validates the product on toggle', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/wishlist/toggle', ['product_id' => 999999])
        ->assertStatus(422);
});

it('deletes a favorite and blocks other users', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $product = makeWishlistProduct();
    $favorite = Favorite::create(['user_id' => $user->id, 'product_id' => $product->id]);

    $this->actingAs($other)->deleteJson('/wishlist/'.$favorite->id)->assertForbidden();

    $this->actingAs($user)
        ->deleteJson('/wishlist/'.$favorite->id)
        ->assertOk()
        ->assertJson(['count' => 0]);
});
