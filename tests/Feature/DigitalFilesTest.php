<?php

use App\Models\Category;
use App\Models\DigitalFile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Support\DigitalDelivery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeFilesAdmin(): User
{
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    return $u;
}

function makeDigitalProductWithFile(DigitalFile $file): Product
{
    $category = Category::create(['name' => 'Software', 'status' => true, 'sort_order' => 0]);

    return Product::create([
        'category_id' => $category->id,
        'title' => 'Test Office',
        'price' => 60.00,
        'stock_status' => 'in_stock',
        'status' => true,
        'is_digital' => true,
        'download_64bit_url' => $file->routeUrl(),
    ]);
}

function makePaidOrderFor(User $user, Product $product, int $qty = 1): Order
{
    $order = Order::create([
        'user_id' => $user->id,
        'customer_email' => $user->email,
        'customer_phone' => '0612345678',
        'subtotal' => 60,
        'total_price' => 60,
        'payment_status' => 'paid',
        'order_status' => 'processing',
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->title,
        'product_price' => 60,
        'quantity' => $qty,
        'total_price' => 60 * $qty,
    ]);

    return $order->fresh();
}

function makeStoredFile(string $name = 'setup.zip'): DigitalFile
{
    Storage::disk('local')->put('digital/test-' . $name, 'fake-binary-content');

    return DigitalFile::create([
        'name' => $name,
        'path' => 'digital/test-' . $name,
        'size' => 19,
        'mime' => 'application/zip',
    ]);
}

it('renders the admin bestanden page', function () {
    $this->actingAs(makeFilesAdmin())
        ->get('/admin/webshop/bestanden')
        ->assertOk()
        ->assertSee('Bestanden');
});

it('uploads a file in chunks and returns a copyable link', function () {
    Storage::fake('local');
    $admin = makeFilesAdmin();

    $chunk = UploadedFile::fake()->createWithContent('part.bin', str_repeat('x', 100 * 1024)); // 100 KB
    $this->actingAs($admin)->postJson('/admin/webshop/bestanden/chunk', [
        'upload_id' => 'test-upload-1',
        'index' => 0,
        'total' => 1,
        'chunk' => $chunk,
    ])->assertOk();

    $res = $this->actingAs($admin)->postJson('/admin/webshop/bestanden/complete', [
        'upload_id' => 'test-upload-1',
        'total' => 1,
        'name' => 'office-2021.zip',
        'size' => $chunk->getSize(),
    ])->assertCreated();

    $res->assertJsonPath('file.name', 'office-2021.zip');
    expect($res->json('url'))->toContain('/download/bestand/');
    $this->assertDatabaseHas('digital_files', ['name' => 'office-2021.zip']);
});

it('rejects disallowed extensions and incomplete uploads', function () {
    Storage::fake('local');
    $admin = makeFilesAdmin();

    $this->actingAs($admin)->postJson('/admin/webshop/bestanden/complete', [
        'upload_id' => 'bad-ext',
        'total' => 1,
        'name' => 'virus.bat',
        'size' => 10,
    ])->assertStatus(422);

    $chunk = UploadedFile::fake()->createWithContent('part.bin', str_repeat('x', 10 * 1024));
    $this->actingAs($admin)->postJson('/admin/webshop/bestanden/chunk', [
        'upload_id' => 'missing-part',
        'index' => 0,
        'total' => 2,
        'chunk' => $chunk,
    ])->assertOk();

    $this->actingAs($admin)->postJson('/admin/webshop/bestanden/complete', [
        'upload_id' => 'missing-part',
        'total' => 2,
        'name' => 'office.zip',
        'size' => 999,
    ])->assertStatus(422);
});

it('protects downloads: owner and admin pass, strangers fail', function () {
    $file = makeStoredFile();
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $stranger = User::factory()->create(['email_verified_at' => now()]);
    $product = makeDigitalProductWithFile($file);
    makePaidOrderFor($owner, $product);

    $url = $file->routeUrl();

    // Anonymous: 403
    $this->get($url)->assertForbidden();
    // Stranger with paid order for another product: 403
    $this->actingAs($stranger)->get($url)->assertForbidden();
    // Owner: downloads
    $this->actingAs($owner)->get($url)
        ->assertOk()
        ->assertHeader('content-disposition');
    // Admin: downloads
    $this->actingAs(makeFilesAdmin())->get($url)->assertOk();

    expect($file->fresh()->downloads_count)->toBe(2);
});

it('serves guest buyers through per-order signed links only', function () {
    $file = makeStoredFile();
    $product = makeDigitalProductWithFile($file);
    $order = makePaidOrderFor(User::factory()->create(['email_verified_at' => now()]), $product);

    // Valid signed link as guest: 200
    $this->get($file->signedUrlForOrder($order))->assertOk();

    // Tampered order number: 403
    $bad = preg_replace('/order=[^&]+/', 'order=ORD-FAKE123', $file->signedUrlForOrder($order));
    $this->get($bad)->assertForbidden();

    // Unpaid order: 403 even with signature
    $unpaid = makePaidOrderFor(User::factory()->create(['email_verified_at' => now()]), $product);
    $unpaid->update(['payment_status' => 'pending']);
    $this->get($file->signedUrlForOrder($unpaid->fresh()))->assertForbidden();
});

it('blocks deleting a file that is linked from a product', function () {
    $file = makeStoredFile();
    makeDigitalProductWithFile($file);

    $this->actingAs(makeFilesAdmin())
        ->deleteJson('/admin/webshop/bestanden/' . $file->id)
        ->assertStatus(422);

    $free = makeStoredFile('vrij.zip');
    $this->actingAs(makeFilesAdmin())
        ->deleteJson('/admin/webshop/bestanden/' . $free->id)
        ->assertOk();
    $this->assertDatabaseMissing('digital_files', ['id' => $free->id]);
});

it('resolves order-aware links via the helper', function () {
    $file = makeStoredFile();
    $product = makeDigitalProductWithFile($file);
    $order = makePaidOrderFor(User::factory()->create(['email_verified_at' => now()]), $product);

    $plain = DigitalDelivery::linksForProduct($product);
    expect($plain[0]['url'])->toContain('/download/bestand/' . $file->id)
        ->and($plain[0]['url'])->not->toContain('signature');

    $signed = DigitalDelivery::linksForProduct($product, $order);
    expect($signed[0]['url'])->toContain('signature=')
        ->and($signed[0]['url'])->toContain($order->order_number);
});
