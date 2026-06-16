<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bước 1: thêm category_id nullable (chưa có FK, để data migration chạy trước)
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('category');
        });

        // Bước 2: data migration — map slug string → id
        $categoryIds = DB::table('categories')->pluck('id', 'slug');
        DB::table('products')->get()->each(function ($product) use ($categoryIds) {
            $id = $categoryIds[$product->category] ?? null;
            if ($id) {
                DB::table('products')->where('id', $product->id)->update(['category_id' => $id]);
            }
        });

        // Bước 3: đổi category_id thành NOT NULL + thêm FK
        DB::statement('ALTER TABLE products MODIFY COLUMN category_id BIGINT UNSIGNED NOT NULL');
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });

        // Bước 4: xóa cột category cũ
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        // Bước 1: thêm lại cột category
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category', ['figure', 'ao', 'manga', 'sticker'])->nullable()->after('category_id');
        });

        // Bước 2: restore data từ categories
        DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'categories.slug')
            ->get()
            ->each(fn ($row) => DB::table('products')->where('id', $row->id)->update(['category' => $row->slug]));

        // Bước 3: make NOT NULL
        DB::statement("ALTER TABLE products MODIFY COLUMN category ENUM('figure','ao','manga','sticker') NOT NULL");

        // Bước 4: drop FK và cột category_id
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
