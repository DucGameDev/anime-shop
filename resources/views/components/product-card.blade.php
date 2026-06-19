@props(['product'])

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

<div class="group flex flex-col rounded-xl bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 overflow-hidden">

    {{-- Ảnh + badge --}}
    <div class="relative overflow-hidden">
        <a href="{{ route('products.show', $product) }}" class="block" tabindex="-1" aria-hidden="true">
            <img
                src="{{ $product->image_url }}"
                alt="{{ $product->name }}"
                class="aspect-square w-full object-cover rounded-t-xl group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        </a>
        <span class="absolute top-2 left-2 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $badgeClass }}">
            {{ $categoryLabel }}
        </span>
    </div>

    {{-- Thông tin --}}
    <div class="flex flex-col flex-1 gap-2 p-3 md:p-4">
        <h3 class="text-sm md:text-base font-medium text-neutral-text line-clamp-2 leading-snug min-h-[2.5rem]">
            <a href="{{ route('products.show', $product) }}" class="hover:text-primary transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <p class="text-base md:text-lg font-bold text-primary-dark">
            {{ number_format($product->price, 0, ',', '.') }}₫
        </p>

        <div class="mt-auto pt-1" x-data="addToCart({{ $product->id }})">
            <button
                type="button"
                @click="add()"
                :disabled="adding"
                class="w-full min-h-[36px] inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <svg x-show="adding" class="h-3.5 w-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="adding ? 'Đang thêm...' : 'Thêm vào giỏ'"></span>
            </button>
        </div>
    </div>
</div>
