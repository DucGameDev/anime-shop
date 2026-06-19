<x-account-layout title="Sản phẩm yêu thích">

    @if ($favorites->isEmpty())
        <div class="rounded-xl bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto mb-4 h-12 w-12 text-neutral-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
            <p class="text-neutral-muted">Chưa có sản phẩm yêu thích.</p>
            <p class="mt-1 text-sm text-neutral-muted">Nhấn vào biểu tượng trái tim trên sản phẩm để lưu vào đây.</p>
            <div class="mt-6">
                <x-button href="{{ route('products.index') }}" variant="primary">
                    Khám phá sản phẩm
                </x-button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
            @foreach ($favorites as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @endif

</x-account-layout>
