<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Seed 4 danh mục ngay trong migration để data migration sau có thể dùng
        DB::table('categories')->insert([
            ['name' => 'Figure',  'slug' => 'figure',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Áo',      'slug' => 'ao',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manga',   'slug' => 'manga',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sticker', 'slug' => 'sticker', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
