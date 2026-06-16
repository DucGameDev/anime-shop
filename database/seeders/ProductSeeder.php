<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'slug');

        $products = [
            // --- Figure (3) ---
            [
                'name'        => 'Figure Naruto Uzumaki – Sage Mode',
                'slug'        => 'figure-naruto-uzumaki-sage-mode',
                'description' => 'Figure chính hãng Naruto ở chế độ Tiên Nhân, tỉ lệ 1/8, cao 22 cm, chất liệu PVC cao cấp.',
                'price'       => 850000.00,
                'image_url'   => 'https://placehold.co/400x400/7E22CE/ffffff?text=Naruto+Figure',
                'category_id' => $categoryIds['figure'],
                'stock'       => 15,
            ],
            [
                'name'        => 'Figure Mikasa Ackerman – Attack on Titan',
                'slug'        => 'figure-mikasa-ackerman-attack-on-titan',
                'description' => 'Figure Mikasa trong trang phục Lính Trinh Sát, tỉ lệ 1/7, cao 25 cm, kèm thanh kiếm phụ kiện.',
                'price'       => 1200000.00,
                'image_url'   => 'https://placehold.co/400x400/7E22CE/ffffff?text=Mikasa+Figure',
                'category_id' => $categoryIds['figure'],
                'stock'       => 8,
            ],
            [
                'name'        => 'Figure Zero Two – Darling in the FranXX',
                'slug'        => 'figure-zero-two-darling-in-the-franxx',
                'description' => 'Figure Zero Two trong trang phục pilot, tỉ lệ 1/7, cao 26 cm, thiết kế chi tiết cặp sừng đặc trưng.',
                'price'       => 1450000.00,
                'image_url'   => 'https://placehold.co/400x400/7E22CE/ffffff?text=Zero+Two+Figure',
                'category_id' => $categoryIds['figure'],
                'stock'       => 5,
            ],

            // --- Áo (3) ---
            [
                'name'        => 'Áo Thun Unisex – Akatsuki Symbols',
                'slug'        => 'ao-thun-unisex-akatsuki-symbols',
                'description' => 'Áo thun cotton 100%, in hình biểu tượng tổ chức Akatsuki, form unisex, size S–2XL.',
                'price'       => 220000.00,
                'image_url'   => 'https://placehold.co/400x400/EC4899/ffffff?text=Akatsuki+Tee',
                'category_id' => $categoryIds['ao'],
                'stock'       => 50,
            ],
            [
                'name'        => 'Áo Hoodie – Demon Slayer Corps',
                'slug'        => 'ao-hoodie-demon-slayer-corps',
                'description' => 'Hoodie 2 lớp in logo Đội Diệt Quỷ, túi kangaroo, dây rút có nắp, size S–XL.',
                'price'       => 450000.00,
                'image_url'   => 'https://placehold.co/400x400/EC4899/ffffff?text=Kimetsu+Hoodie',
                'category_id' => $categoryIds['ao'],
                'stock'       => 30,
            ],
            [
                'name'        => 'Áo Thun Oversize – One Piece Going Merry',
                'slug'        => 'ao-thun-oversize-one-piece-going-merry',
                'description' => 'Áo thun oversize in hình tàu Going Merry phong cách vintage, chất liệu cotton co giãn, size M–2XL.',
                'price'       => 260000.00,
                'image_url'   => 'https://placehold.co/400x400/EC4899/ffffff?text=Going+Merry+Tee',
                'category_id' => $categoryIds['ao'],
                'stock'       => 40,
            ],

            // --- Manga (3) ---
            [
                'name'        => 'Manga Fullmetal Alchemist – Tập 1',
                'slug'        => 'manga-fullmetal-alchemist-tap-1',
                'description' => 'Truyện tranh Fullmetal Alchemist tập 1, bản dịch tiếng Việt, bìa cứng, NXB Kim Đồng.',
                'price'       => 45000.00,
                'image_url'   => 'https://placehold.co/400x400/1D4ED8/ffffff?text=FMA+Vol+1',
                'category_id' => $categoryIds['manga'],
                'stock'       => 100,
            ],
            [
                'name'        => 'Manga Jujutsu Kaisen – Tập 1',
                'slug'        => 'manga-jujutsu-kaisen-tap-1',
                'description' => 'Truyện tranh Jujutsu Kaisen tập 1, bản tiếng Việt chính thức, bìa mềm, NXB Kim Đồng.',
                'price'       => 45000.00,
                'image_url'   => 'https://placehold.co/400x400/1D4ED8/ffffff?text=JJK+Vol+1',
                'category_id' => $categoryIds['manga'],
                'stock'       => 80,
            ],
            [
                'name'        => 'Manga Chainsaw Man – Tập 1',
                'slug'        => 'manga-chainsaw-man-tap-1',
                'description' => 'Truyện tranh Chainsaw Man tập 1, bản tiếng Việt, bìa mềm, dành cho độc giả 16+.',
                'price'       => 48000.00,
                'image_url'   => 'https://placehold.co/400x400/1D4ED8/ffffff?text=CSM+Vol+1',
                'category_id' => $categoryIds['manga'],
                'stock'       => 60,
            ],

            // --- Sticker (3) ---
            [
                'name'        => 'Sticker Pack – Chibi Demon Slayer (12 tấm)',
                'slug'        => 'sticker-pack-chibi-demon-slayer',
                'description' => 'Bộ 12 sticker chibi các nhân vật Kimetsu no Yaiba, chất liệu vinyl chống nước, kích thước 5–8 cm.',
                'price'       => 35000.00,
                'image_url'   => 'https://placehold.co/400x400/CA8A04/ffffff?text=KnY+Stickers',
                'category_id' => $categoryIds['sticker'],
                'stock'       => 200,
            ],
            [
                'name'        => 'Sticker Pack – My Hero Academia Symbols (10 tấm)',
                'slug'        => 'sticker-pack-my-hero-academia-symbols',
                'description' => 'Bộ 10 sticker biểu tượng quirk các hero trong Boku no Hero Academia, vinyl chống nước.',
                'price'       => 30000.00,
                'image_url'   => 'https://placehold.co/400x400/CA8A04/ffffff?text=MHA+Stickers',
                'category_id' => $categoryIds['sticker'],
                'stock'       => 150,
            ],
            [
                'name'        => 'Sticker Holographic – Spirited Away (6 tấm)',
                'slug'        => 'sticker-holographic-spirited-away',
                'description' => 'Bộ 6 sticker holographic nhân vật Sen to Chihiro, ánh bảy màu khi dưới ánh sáng.',
                'price'       => 55000.00,
                'image_url'   => 'https://placehold.co/400x400/CA8A04/ffffff?text=Chihiro+Stickers',
                'category_id' => $categoryIds['sticker'],
                'stock'       => 120,
            ],
        ];

        foreach ($products as $data) {
            Product::create($data);
        }
    }
}
