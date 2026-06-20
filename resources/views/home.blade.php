<x-app-layout title="Trang chủ — AnimeShop">

    {{-- ===== BANNER ===== --}}
    <section class="relative overflow-hidden">
        <img src="/images/banners/banner.png"
             alt="AnimeShop — Thế giới anime dành cho bạn"
             class="w-full block h-44 sm:h-64 lg:h-80 xl:h-96 object-cover object-top"
             loading="eager">

        {{-- Gradient đảm bảo text đọc được trên mọi màn hình --}}
        <div class="absolute inset-0 bg-gradient-to-r from-white from-40% via-white/60 via-60% to-transparent"></div>

        {{-- Text overlay — vùng trắng bên trái banner --}}
        <div class="absolute inset-0 flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="w-[58%] sm:w-[44%] lg:w-[30%] bg-white/70 backdrop-blur-sm rounded-xl px-4 py-4 shadow-[0_0_32px_16px_rgba(255,255,255,0.85)]">
                <h1 class="text-sm sm:text-xl md:text-2xl lg:text-3xl font-bold text-primary-dark leading-snug">
                    Thiên đường đồ anime chính hãng tại Việt Nam
                </h1>
                <p class="mt-1.5 text-xs md:text-sm text-neutral-text hidden sm:block leading-relaxed">
                    Figure, áo, manga, sticker — tất cả trong một shop.
                    Giao hàng toàn quốc, đổi trả 7 ngày.
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <x-button variant="primary" size="sm" href="{{ route('products.index') }}">
                        Khám phá ngay
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </x-button>
                </div>
            </div>
            </div>
        </div>
    </section>

    {{-- ===== PROMO BANNERS ===== --}}
    <section class="py-6 lg:py-8">
        <x-container>
            <div class="flex flex-col lg:flex-row gap-3">

                {{-- Banner lớn bên trái — tỉ lệ 2:1 --}}
                <a href="{{ route('products.index') }}"
                   class="block lg:w-3/5 flex-shrink-0 rounded-xl overflow-hidden">
                    <img src="/images/banners/promo-freeship.jpg"
                         alt="Freeship toàn quốc đơn từ 300K"
                         class="w-full aspect-[2/1] object-cover hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                </a>

                {{-- 2 banner nhỏ bên phải — tỉ lệ 8:3 để khớp chiều cao banner trái --}}
                <div class="flex-1 flex flex-col gap-3">
                    <a href="{{ route('products.index') }}"
                       class="block rounded-xl overflow-hidden">
                        <img src="/images/banners/promo-authentic.jpg"
                             alt="Hàng chính hãng 100% Authentic"
                             class="w-full aspect-[8/3] object-cover object-center hover:scale-105 transition-transform duration-300"
                             loading="lazy">
                    </a>
                    <a href="{{ route('products.index') }}?category=figure"
                       class="block rounded-xl overflow-hidden">
                        <img src="/images/banners/promo-figure.jpg"
                             alt="Figure mới về mỗi tuần"
                             class="w-full aspect-[8/3] object-cover object-center hover:scale-105 transition-transform duration-300"
                             loading="lazy">
                    </a>
                </div>

            </div>
        </x-container>
    </section>

    {{-- ===== SẢN PHẨM NỔI BẬT ===== --}}
    <section class="py-8 lg:py-16">
        <x-container>

            <div class="flex items-center justify-between mb-6 lg:mb-8">
                <h2 class="text-xl lg:text-2xl font-bold text-neutral-text">
                    Sản phẩm nổi bật
                </h2>
                <a href="{{ route('products.index') }}"
                   class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                    Xem tất cả →
                </a>
            </div>

            @if ($products->isEmpty())
                <p class="text-center text-neutral-muted py-12">
                    Chưa có sản phẩm nào. Quay lại sau nhé!
                </p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <div class="mt-10">
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                @endif
            @endif

        </x-container>
    </section>

    {{-- ===== BANNER DANH MỤC ===== --}}
    <section class="py-8 lg:py-12 bg-white border-t border-gray-100">
        <x-container>
            <h2 class="text-xl lg:text-2xl font-bold text-neutral-text mb-6 text-center">
                Mua theo danh mục
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ([
                    ['label' => 'Figure',  'cat' => 'figure',  'img' => 'cat-figure.jpg', 'fallback' => 'from-primary-dark via-primary to-purple-400'],
                    ['label' => 'Áo',      'cat' => 'ao',      'img' => 'cat-ao.jpg',     'fallback' => 'from-secondary via-pink-400 to-pink-300'],
                    ['label' => 'Manga',   'cat' => 'manga',   'img' => 'cat-manga.jpg',  'fallback' => 'from-info via-blue-400 to-blue-300'],
                    ['label' => 'Sticker', 'cat' => 'sticker', 'img' => 'cat-sticker.jpg','fallback' => 'from-warning via-amber-400 to-yellow-300'],
                ] as $item)
                    <a href="{{ route('products.index', ['category' => $item['cat']]) }}"
                       class="relative group overflow-hidden rounded-xl aspect-[3/4] block">

                        {{-- Ảnh nền hoặc gradient fallback --}}
                        @if (file_exists(public_path('images/categories/' . $item['img'])))
                            <img src="/images/categories/{{ $item['img'] }}"
                                 alt="{{ $item['label'] }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br {{ $item['fallback'] }}"></div>
                        @endif

                        {{-- Gradient overlay từ dưới lên --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                        {{-- Label --}}
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-white font-bold text-lg leading-tight">{{ $item['label'] }}</p>
                            <p class="text-white/70 text-xs mt-0.5">Xem tất cả →</p>
                        </div>

                    </a>
                @endforeach
            </div>
        </x-container>
    </section>

</x-app-layout>
