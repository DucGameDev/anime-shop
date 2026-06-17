<x-app-layout
    :title="$product->name"
    :description="\Illuminate\Support\Str::limit(strip_tags($product->description), 155)">

    {{-- JSON-LD: Product --}}
    @push('head')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ $product->name }}",
        "description": "{{ addslashes(strip_tags($product->description)) }}",
        "image": "{{ $product->image_url }}",
        "url": "{{ url()->current() }}",
        "offers": {
            "@type": "Offer",
            "price": "{{ $product->price }}",
            "priceCurrency": "VND",
            "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "seller": { "@type": "Organization", "name": "{{ config('app.name') }}" }
        },
        "breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [
                { "@type": "ListItem", "position": 1, "name": "Trang chủ", "item": "{{ route('home') }}" },
                { "@type": "ListItem", "position": 2, "name": "Sản phẩm", "item": "{{ route('products.index') }}" },
                { "@type": "ListItem", "position": 3, "name": "{{ $product->name }}", "item": "{{ url()->current() }}" }
            ]
        }
    }
    </script>
    @endpush

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
            <div class="w-full lg:w-1/2">
                <img
                    src="{{ $product->image_url }}"
                    alt="{{ $product->name }}"
                    class="aspect-square w-full object-cover rounded-lg shadow-sm"
                >
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

</x-app-layout>
