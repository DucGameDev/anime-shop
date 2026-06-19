<div>
    @if (empty($items))
        {{-- ===== GIỎ HÀNG TRỐNG ===== --}}
        <div class="flex flex-col items-center justify-center py-20 text-center gap-5">
            {{-- Icon giỏ hàng rỗng --}}
            <div class="relative">
                <svg class="h-24 w-24 text-neutral-muted/30" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                </svg>
                <span class="absolute -top-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full bg-neutral-muted/20">
                    <svg class="h-4 w-4 text-neutral-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </span>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-neutral-text">Giỏ hàng của bạn đang trống</h2>
                <p class="mt-1 text-sm text-neutral-muted">
                    Hãy thêm sản phẩm yêu thích vào giỏ để bắt đầu mua sắm!
                </p>
            </div>

            <x-button variant="primary">
                <a href="{{ route('products.index') }}" class="flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Quay lại cửa hàng
                </a>
            </x-button>
        </div>

    @else
        {{-- ===== GIỎ HÀNG CÓ SẢN PHẨM ===== --}}
        <div class="flex flex-col lg:flex-row gap-8" wire:loading.class="opacity-60 pointer-events-none">

            {{-- ========== CỘT TRÁI: DANH SÁCH SẢN PHẨM ========== --}}
            <div class="lg:w-2/3 space-y-3">

                <p class="text-sm text-neutral-muted">
                    {{ $itemCount }} sản phẩm trong giỏ
                </p>

                @foreach ($items as $item)
                    <div
                        class="flex items-start gap-3 rounded-xl bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
                        wire:key="cart-page-{{ $item['product_id'] }}"
                    >
                        {{-- Ảnh --}}
                        <a href="{{ route('products.show', ['product' => $item['slug']]) }}" class="flex-shrink-0">
                            <img
                                src="{{ $item['image_url'] }}"
                                alt="{{ $item['name'] }}"
                                class="h-16 w-16 rounded-md object-cover"
                                loading="lazy"
                            >
                        </a>

                        {{-- Nội dung --}}
                        <div class="flex-1 min-w-0">
                            {{-- Tên + nút xóa --}}
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('products.show', ['product' => $item['slug']]) }}"
                                   class="text-sm font-medium text-neutral-text hover:text-primary transition-colors line-clamp-2 leading-snug">
                                    {{ $item['name'] }}
                                </a>

                                <button
                                    wire:click="removeItem({{ $item['product_id'] }})"
                                    class="flex-shrink-0 rounded-md p-1 text-red-300 hover:bg-red-50 hover:text-red-500 transition-colors"
                                    aria-label="Xóa {{ $item['name'] }}"
                                    title="Xóa khỏi giỏ"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Giá đơn + bộ chọn số lượng --}}
                            <div class="mt-2 flex items-center justify-between gap-4">
                                <span class="text-sm font-bold text-primary-dark">
                                    {{ number_format($item['price'], 0, ',', '.') }}₫
                                </span>

                                {{-- Qty -/+ --}}
                                <div class="flex items-center gap-1.5">
                                    <button
                                        wire:click="decrementQty({{ $item['product_id'] }})"
                                        class="flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 text-neutral-text transition-colors hover:border-primary hover:text-primary disabled:cursor-not-allowed disabled:opacity-40"
                                        {{ $item['quantity'] <= 1 ? 'disabled' : '' }}
                                        aria-label="Giảm số lượng"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                        </svg>
                                    </button>

                                    <span class="w-8 text-center text-sm font-semibold tabular-nums text-neutral-text select-none">
                                        {{ $item['quantity'] }}
                                    </span>

                                    <button
                                        wire:click="incrementQty({{ $item['product_id'] }})"
                                        class="flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 text-neutral-text transition-colors hover:border-primary hover:text-primary disabled:opacity-40 disabled:cursor-not-allowed"
                                        {{ $item['quantity'] >= ($stocks[$item['product_id']] ?? 0) ? 'disabled' : '' }}
                                        aria-label="Tăng số lượng"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Cảnh báo hết stock --}}
                            @if ($item['quantity'] >= ($stocks[$item['product_id']] ?? 0))
                                <p class="mt-1 text-xs text-warning font-medium">
                                    ⚠ Đã đạt số lượng tối đa trong kho
                                </p>
                            @endif

                            {{-- Subtotal --}}
                            <p class="mt-1 text-right text-xs text-neutral-muted">
                                Thành tiền:
                                <span class="font-medium text-neutral-text">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                </span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ========== CỘT PHẢI: TÓM TẮT ĐƠN HÀNG ========== --}}
            <div class="lg:w-1/3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 lg:sticky lg:top-20">
                    <h2 class="mb-4 text-base font-semibold text-neutral-text">Tóm tắt đơn hàng</h2>

                    {{-- Chi tiết --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-neutral-muted">
                            <span>Số sản phẩm</span>
                            <span class="font-medium text-neutral-text">{{ $itemCount }}</span>
                        </div>

                        @foreach ($items as $item)
                            <div class="flex justify-between text-neutral-muted">
                                <span class="truncate max-w-[160px]">{{ $item['name'] }}</span>
                                <span class="flex-shrink-0 ml-2 tabular-nums">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tổng --}}
                    <div class="my-4 border-t border-dashed border-gray-200"></div>
                    <div class="flex items-baseline justify-between">
                        <span class="text-sm font-medium text-neutral-text">Tổng cộng</span>
                        <span class="text-xl font-bold text-primary-dark tabular-nums">
                            {{ number_format($total, 0, ',', '.') }}₫
                        </span>
                    </div>

                    {{-- Shipping note --}}
                    <p class="mt-2 text-xs text-neutral-muted">
                        Phí vận chuyển sẽ được tính ở bước thanh toán.
                    </p>

                    {{-- CTA --}}
                    <div class="mt-5">
                        <x-button variant="primary" class="w-full">
                            <a href="{{ route('checkout.index') }}" class="flex items-center justify-center gap-1.5 w-full">
                                Tiến hành thanh toán
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                </svg>
                            </a>
                        </x-button>
                    </div>

                    <a href="{{ route('products.index') }}"
                       class="mt-3 block text-center text-sm text-neutral-muted hover:text-primary transition-colors">
                        ← Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
