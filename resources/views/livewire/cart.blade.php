<div>
    @if (empty($items))
        {{-- Giỏ hàng trống --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="h-20 w-20 text-neutral-muted/40 mb-4" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
            </svg>
            <p class="text-base font-medium text-neutral-text mb-1">Giỏ hàng của bạn đang trống</p>
            <p class="text-sm text-neutral-muted mb-5">Hãy thêm sản phẩm yêu thích vào giỏ nhé!</p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                Xem sản phẩm →
            </a>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ===== Danh sách sản phẩm ===== --}}
            <div class="lg:w-2/3 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-neutral-text">
                        {{ count($items) }} sản phẩm
                    </h2>
                    <button
                        wire:click="clearCart"
                        wire:confirm="Xóa toàn bộ giỏ hàng?"
                        class="text-sm text-neutral-muted hover:text-red-500 transition-colors"
                    >
                        Xóa tất cả
                    </button>
                </div>

                @foreach ($items as $item)
                    <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm"
                         wire:key="cart-item-{{ $item['product_id'] }}">

                        {{-- Ảnh --}}
                        <a href="{{ route('products.show', ['product' => $item['slug']]) }}" class="flex-shrink-0">
                            <img src="{{ $item['image_url'] }}"
                                 alt="{{ $item['name'] }}"
                                 class="h-20 w-20 rounded-lg object-cover"
                                 loading="lazy">
                        </a>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', ['product' => $item['slug']]) }}"
                               class="text-sm font-medium text-neutral-text hover:text-primary transition-colors line-clamp-2">
                                {{ $item['name'] }}
                            </a>
                            <p class="mt-1 text-sm font-bold text-primary-dark">
                                {{ number_format($item['price'], 0, ',', '.') }}₫
                            </p>

                            {{-- Qty controls --}}
                            <div class="mt-2 flex items-center gap-2">
                                <button
                                    type="button"
                                    wire:click="$set('quantities.{{ $item['product_id'] }}', {{ max(1, ($quantities[$item['product_id']] ?? 1) - 1) }}); updateQuantity({{ $item['product_id'] }})"
                                    class="flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 text-neutral-text hover:border-primary hover:text-primary transition-colors"
                                    :disabled="{{ ($quantities[$item['product_id']] ?? 1) <= 1 ? 'true' : 'false' }}"
                                >
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                    </svg>
                                </button>

                                <input
                                    type="number"
                                    wire:model.lazy="quantities.{{ $item['product_id'] }}"
                                    wire:change="updateQuantity({{ $item['product_id'] }})"
                                    min="1"
                                    class="h-7 w-12 rounded-md border border-gray-300 text-center text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/20"
                                >

                                <button
                                    type="button"
                                    wire:click="$set('quantities.{{ $item['product_id'] }}', {{ ($quantities[$item['product_id']] ?? 1) + 1 }}); updateQuantity({{ $item['product_id'] }})"
                                    class="flex h-7 w-7 items-center justify-center rounded-md border border-gray-300 text-neutral-text hover:border-primary hover:text-primary transition-colors"
                                >
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Subtotal + remove --}}
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <p class="text-sm font-bold text-neutral-text">
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                            </p>
                            <button
                                wire:click="removeItem({{ $item['product_id'] }})"
                                class="text-neutral-muted hover:text-red-500 transition-colors"
                                aria-label="Xóa {{ $item['name'] }}"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ===== Tóm tắt đơn hàng ===== --}}
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-5 sticky top-20">
                    <h2 class="text-base font-semibold text-neutral-text mb-4">Tóm tắt đơn hàng</h2>

                    <div class="space-y-2 text-sm">
                        @foreach ($items as $item)
                            <div class="flex justify-between text-neutral-muted">
                                <span class="truncate max-w-[180px]">{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                                <span class="flex-shrink-0 ml-2">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="my-4 border-t border-dashed border-gray-200"></div>

                    <div class="flex justify-between text-base font-bold text-neutral-text">
                        <span>Tổng cộng</span>
                        <span class="text-primary-dark">{{ number_format($total, 0, ',', '.') }}₫</span>
                    </div>

                    <button
                        class="mt-5 w-full min-h-[44px] rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                    >
                        Tiến hành thanh toán →
                    </button>

                    <a href="{{ route('products.index') }}"
                       class="mt-3 block text-center text-sm text-neutral-muted hover:text-primary transition-colors">
                        ← Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
