<x-app-layout :title="$product->name . ' — AnimeShop'">

    <x-container class="py-8 lg:py-12">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center gap-1.5 text-sm text-neutral-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('products.index') }}" class="hover:text-primary transition-colors">Sản phẩm</a>
            <span>/</span>
            <span class="text-neutral-text line-clamp-1">{{ $product->name }}</span>
        </nav>

        {{-- Main layout --}}
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

            {{-- ===== Cột ảnh ===== --}}
            <div class="w-full lg:w-1/2" x-data="{ lightbox: false }">

                {{-- Ảnh — click để mở lightbox --}}
                <div class="relative cursor-zoom-in group/img" @click="lightbox = true">
                    <img
                        src="{{ $product->image_url }}"
                        alt="{{ $product->name }}"
                        class="aspect-square w-full object-cover rounded-lg shadow-sm"
                    >
                    {{-- Zoom hint --}}
                    <div class="absolute bottom-2 right-2 rounded-md bg-black/40 p-1.5 text-white opacity-0 group-hover/img:opacity-100 transition-opacity">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6"/>
                        </svg>
                    </div>
                </div>

                {{-- Lightbox --}}
                <div
                    x-show="lightbox"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="lightbox = false"
                    @keydown.escape.window="lightbox = false"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4"
                    style="display:none"
                >
                    <div class="relative max-w-3xl w-full" @click.stop>
                        <img
                            src="{{ $product->image_url }}"
                            alt="{{ $product->name }}"
                            class="w-full rounded-xl object-contain max-h-[85vh] shadow-2xl"
                        >
                        <button
                            @click="lightbox = false"
                            class="absolute -top-3 -right-3 flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-lg text-neutral-text hover:text-primary transition-colors"
                            aria-label="Đóng"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>

            {{-- ===== Cột thông tin ===== --}}
            <div class="w-full lg:w-1/2 flex flex-col gap-5">

                {{-- Badge category --}}
                @php
                $categorySlug  = $product->category?->slug ?? '';
                $categoryLabel = $product->category?->name ?? '';
                $badgeClass    = match ($categorySlug) {
                    'figure'  => 'bg-primary-light text-primary-dark',
                    'ao'      => 'bg-secondary-light text-secondary',
                    'manga'   => 'bg-info-light text-info',
                    'sticker' => 'bg-warning-light text-warning',
                    default   => 'bg-neutral-bg text-neutral-text',
                };
                @endphp

                <span class="inline-flex w-fit items-center rounded-md px-2.5 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                    {{ $categoryLabel }}
                </span>

                {{-- Tên --}}
                <h1 class="text-xl lg:text-3xl font-bold text-neutral-text leading-snug">
                    {{ $product->name }}
                </h1>

                {{-- Giá --}}
                <p class="text-2xl font-bold text-primary-dark">
                    {{ number_format($product->price, 0, ',', '.') }}₫
                </p>

                {{-- Mô tả --}}
                <p class="text-sm md:text-base text-neutral-text leading-relaxed">
                    {{ $product->description }}
                </p>

                {{-- Tồn kho --}}
                <p class="text-sm text-neutral-muted">
                    @if ($product->stock > 10)
                        <span class="text-green-600 font-medium">● Còn hàng</span>
                        ({{ $product->stock }} sản phẩm)
                    @elseif ($product->stock > 0)
                        <span class="text-warning font-medium">● Còn ít hàng</span>
                        — chỉ còn {{ $product->stock }} sản phẩm
                    @else
                        <span class="text-red-500 font-medium">● Hết hàng</span>
                    @endif
                </p>

                {{-- Quantity selector + Add to cart --}}
                @if ($product->stock > 0)
                    <div x-data="productDetail({{ $product->id }}, {{ $product->stock }})" x-init="max = $data.max">
                        <p class="mb-2 text-sm font-medium text-neutral-text">Số lượng</p>

                        <div class="flex items-center gap-2">
                            {{-- Nút giảm --}}
                            <button
                                type="button"
                                @click="qty = Math.max(1, qty - 1)"
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-neutral-text hover:border-primary hover:text-primary transition-colors disabled:opacity-40"
                                :disabled="qty <= 1"
                                aria-label="Giảm số lượng"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                </svg>
                            </button>

                            {{-- Input số lượng --}}
                            <x-input
                                type="number"
                                x-model="qty"
                                min="1"
                                :max="$product->stock"
                                class="!w-16 text-center"
                                aria-label="Số lượng"
                            />

                            {{-- Nút tăng --}}
                            <button
                                type="button"
                                @click="qty = Math.min(max, qty + 1)"
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-neutral-text hover:border-primary hover:text-primary transition-colors disabled:opacity-40"
                                :disabled="qty >= max"
                                aria-label="Tăng số lượng"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Nút thêm vào giỏ --}}
                        <div class="mt-5">
                            <button
                                type="button"
                                @click="add()"
                                :disabled="adding"
                                class="w-full lg:w-auto min-w-[200px] min-h-[44px] inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm md:text-base font-medium text-white transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <svg x-show="adding" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <svg x-show="!adding" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                                </svg>
                                <span x-text="adding ? 'Đang thêm...' : 'Thêm vào giỏ'"></span>
                            </button>
                        </div>
                    </div>
                @else
                    <x-button variant="secondary" class="w-full lg:w-auto min-w-[200px] opacity-60 cursor-not-allowed" disabled>
                        Hết hàng
                    </x-button>
                @endif

            </div>
        </div>

    </x-container>

    {{-- ===== ĐÁNH GIÁ SẢN PHẨM ===== --}}
    <div class="border-t border-gray-200 mt-10">
        <x-container class="py-10">
            <h2 class="text-xl font-bold text-neutral-text mb-6">Đánh giá sản phẩm</h2>
            <livewire:product-reviews :product="$product" />
        </x-container>
    </div>

    {{-- ===== GỢI Ý SẢN PHẨM ===== --}}
    @if ($suggestions->isNotEmpty())
        <div class="border-t border-gray-200 bg-white">
            <x-container class="py-10 lg:py-14">
                <h2 class="mb-6 text-xl font-bold text-neutral-text">Có thể bạn cũng thích</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    @foreach ($suggestions as $suggested)
                        <x-product-card :product="$suggested" />
                    @endforeach
                </div>
            </x-container>
        </div>
    @endif

</x-app-layout>
