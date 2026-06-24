<x-app-layout
    title="Tất cả sản phẩm — AnimeShop"
    description="Khám phá hàng ngàn sản phẩm anime chính hãng: figure, áo, manga, sticker. Bộ lọc theo danh mục, tìm kiếm nhanh, giao hàng toàn quốc."
>

    <x-container class="py-8 lg:py-12">

        <div class="mb-6 lg:mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold text-neutral-text">
                Tất cả sản phẩm
            </h1>
            <p class="mt-1 text-sm text-neutral-muted">
                Khám phá figure, áo, manga và sticker anime chính hãng.
            </p>
        </div>

        <livewire:product-list />

    </x-container>

</x-app-layout>
