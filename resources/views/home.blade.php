<x-app-layout title="Trang chủ — AnimeShop">

    {{-- ===== BANNER ===== --}}
    <section class="relative overflow-hidden">
        <img src="/banner.png"
             alt="AnimeShop — Thế giới anime dành cho bạn"
             class="w-full block h-44 sm:h-64 lg:h-80 xl:h-96 object-cover object-top">

        {{-- Gradient đảm bảo text đọc được trên mọi màn hình --}}
        <div class="absolute inset-0 bg-gradient-to-r from-white from-40% via-white/60 via-60% to-transparent"></div>

        {{-- Text overlay — vùng trắng bên trái banner --}}
        <div class="absolute inset-0 flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="w-[58%] sm:w-[44%] lg:w-[30%] bg-white/70 backdrop-blur-sm rounded-xl px-4 py-4 shadow-[0_0_32px_16px_rgba(255,255,255,0.85)]">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-primary text-white mb-2">
                    🎌 Hàng mới về mỗi tuần
                </span>
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
                    <x-button variant="secondary" size="sm" href="{{ route('products.index') }}?category=figure">
                        Xem figure
                    </x-button>
                </div>
            </div>
            </div>
        </div>
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
                        {{ $products->links() }}
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
                    ['label' => 'Figure',   'cat' => 'figure',  'icon' => '🗿', 'bg' => 'bg-primary-light',   'text' => 'text-primary-dark'],
                    ['label' => 'Áo',       'cat' => 'ao',      'icon' => '👕', 'bg' => 'bg-secondary-light', 'text' => 'text-secondary'],
                    ['label' => 'Manga',    'cat' => 'manga',   'icon' => '📚', 'bg' => 'bg-info-light',      'text' => 'text-info'],
                    ['label' => 'Sticker',  'cat' => 'sticker', 'icon' => '🎴', 'bg' => 'bg-warning-light',   'text' => 'text-warning'],
                ] as $item)
                    <a href="{{ route('products.index') }}?category={{ $item['cat'] }}"
                       class="flex flex-col items-center gap-2 rounded-xl py-6 px-4 {{ $item['bg'] }} hover:opacity-90 transition-opacity text-center">
                        <span class="text-3xl">{{ $item['icon'] }}</span>
                        <span class="font-semibold text-sm {{ $item['text'] }}">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </x-container>
    </section>

</x-app-layout>
