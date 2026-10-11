<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function stockValues(bool $withComingSoon): array
    {
        return $withComingSoon
            ? ['in_stock', 'out_of_stock', 'coming_soon']
            : ['in_stock', 'out_of_stock'];
    }

    private function createProductsTable(bool $withComingSoon): void
    {
        $values = $this->stockValues($withComingSoon);
        Schema::create('products', function (Blueprint $table) use ($values) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('brand')->nullable();
            $table->string('sku', 64)->nullable()->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->dateTime('discount_start_date')->nullable();
            $table->dateTime('discount_end_date')->nullable();
            $table->enum('stock_status', $values)->default('in_stock');
            $table->boolean('status')->default(true);
            $table->boolean('is_digital')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->json('highlights')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->json('colors')->nullable();
            $table->json('sizes')->nullable();
            $table->string('main_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('external_link')->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('download_32bit_url')->nullable();
            $table->string('download_64bit_url')->nullable();
            $table->string('manual_url')->nullable();
            $table->timestamps();

            $table->index('category_id');
            $table->index(['status', 'stock_status']);
            $table->index('brand');
            $table->index('price');
            $table->index(['rating_avg', 'rating_count']);
        });
    }

    private function rebuild(bool $withComingSoon): void
    {
        $rows = DB::table('products')->get()->map(fn ($r) => (array) $r)->all();
        Schema::disableForeignKeyConstraints();
        Schema::drop('products');
        $this->createProductsTable($withComingSoon);
        if (! empty($rows)) {
            foreach (array_chunk($rows, 200) as $chunk) {
                DB::table('products')->insert($chunk);
            }
        }
        Schema::enableForeignKeyConstraints();
    }

    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite emulates ENUM with a CHECK constraint — rebuild the
            // table to widen the allowed values (data is preserved).
            $this->rebuild(true);
            return;
        }

        DB::statement("ALTER TABLE `products` MODIFY `stock_status` ENUM('in_stock','out_of_stock','coming_soon') NOT NULL DEFAULT 'in_stock'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::table('products')->where('stock_status', 'coming_soon')->update(['stock_status' => 'out_of_stock']);
            $this->rebuild(false);
            return;
        }

        DB::table('products')->where('stock_status', 'coming_soon')->update(['stock_status' => 'out_of_stock']);
        DB::statement("ALTER TABLE `products` MODIFY `stock_status` ENUM('in_stock','out_of_stock') NOT NULL DEFAULT 'in_stock'");
    }
};
