<div>
    {{-- ===== FILTER + SEARCH ===== --}}
    <div class="flex flex-col gap-4 mb-6 lg:mb-8">

        {{-- Category pills — scroll ngang trên mobile --}}
        <div class="overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex gap-2 w-max sm:w-auto sm:flex-wrap">
                {{-- Pill "Tất cả" --}}
                <button
                    wire:click="setCategory('')"
                    class="flex-shrink-0 min-h-[40px] px-4 py-1.5 rounded-full text-sm font-medium border transition-colors
                        {{ $category === ''
                            ? 'bg-primary text-white border-primary shadow-sm'
                            : 'bg-white text-neutral-text border-gray-300 hover:border-primary hover:text-primary' }}"
                >
                    Tất cả
                </button>

                {{-- Pills từ DB --}}
                @foreach ($categories as $cat)
                    <button
                        wire:click="setCategory('{{ $cat->slug }}')"
                        class="flex-shrink-0 min-h-[40px] px-4 py-1.5 rounded-full text-sm font-medium border transition-colors
                            {{ $category === $cat->slug
                                ? 'bg-primary text-white border-primary shadow-sm'
                                : 'bg-white text-neutral-text border-gray-300 hover:border-primary hover:text-primary' }}"
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Search + Sort --}}
        <div class="flex gap-3">
            {{-- Search input --}}
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-neutral-muted" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Tìm sản phẩm..."
                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-text placeholder-neutral-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors"
                >
                @if ($search)
                    <button
                        wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-neutral-muted hover:text-neutral-text"
                        aria-label="Xóa tìm kiếm"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Sort select --}}
            <select
                wire:model.live="sort"
                class="flex-shrink-0 rounded-lg border border-gray-300 bg-white py-2.5 pl-3 pr-4 text-sm text-neutral-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors cursor-pointer"
            >
                <option value="newest">Mới nhất</option>
                <option value="popular">Phổ biến</option>
                <option value="price_asc">Giá tăng dần</option>
                <option value="price_desc">Giá giảm dần</option>
                <option value="random">Ngẫu nhiên</option>
            </select>
        </div>
    </div>

    {{-- ===== PRODUCT GRID ===== --}}
    @if ($products->total() === 0)
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <svg class="h-20 w-20 text-neutral-muted/50 mb-4" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <p class="text-base font-medium text-neutral-text mb-1">
                Không tìm thấy sản phẩm phù hợp
            </p>
            <p class="text-sm text-neutral-muted">
                Thử tìm từ khóa khác hoặc chọn danh mục khác.
            </p>
            <button
                wire:click="$set('search', ''); setCategory('')"
                class="mt-4 text-sm font-medium text-primary hover:text-primary-dark transition-colors"
            >
                Xem tất cả sản phẩm
            </button>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        {{-- Pagination + summary --}}
        <p class="mt-6 text-sm text-neutral-muted text-center">
            Hiển thị {{ $products->firstItem() }}–{{ $products->lastItem() }}
            trong {{ $products->total() }} sản phẩm
            @if ($category) · <span class="font-medium text-neutral-text">{{ $categories->firstWhere('slug', $category)?->name ?? $category }}</span> @endif
            @if ($search) · khớp "<span class="font-medium text-neutral-text">{{ $search }}</span>" @endif
        </p>

        @if ($products->hasPages())
            <div class="mt-2">
                {{ $products->onEachSide(1)->links('livewire-pagination') }}
            </div>
        @endif
    @endif
</div>
