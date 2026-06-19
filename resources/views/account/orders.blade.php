<x-account-layout title="Đơn hàng của tôi">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl lg:text-3xl font-bold text-neutral-text">Đơn hàng của tôi</h1>
        <span class="text-sm text-neutral-muted">{{ auth()->user()->name }}</span>
    </div>

    @if ($orders->isEmpty())
        <div class="rounded-xl bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto mb-4 h-12 w-12 text-neutral-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
            <p class="text-neutral-muted">Bạn chưa có đơn hàng nào.</p>
            <div class="mt-4">
                <x-button href="{{ route('products.index') }}" variant="primary">Mua sắm ngay</x-button>
            </div>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                @php
                    $statusConfig = match($order->status) {
                        'pending'   => ['label' => 'Chờ xử lý', 'class' => 'bg-amber-100 text-amber-700'],
                        'shipped'   => ['label' => 'Đang giao',  'class' => 'bg-blue-100 text-blue-700'],
                        'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-green-100 text-green-700'],
                        'cancelled' => ['label' => 'Đã huỷ',     'class' => 'bg-red-100 text-red-600'],
                        default     => ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-600'],
                    };
                    $thumbs = $order->items->take(4);
                    $extra  = $order->items->count() - $thumbs->count();
                @endphp

                <div class="rounded-xl bg-white shadow-sm overflow-hidden">

                    {{-- Card chính — click để xem chi tiết --}}
                    <a href="{{ route('orders.show', $order) }}"
                       class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors group">

                        {{-- Thumbnails --}}
                        <div class="flex flex-shrink-0 -space-x-2">
                            @foreach ($thumbs as $item)
                                <img src="{{ $item->product?->image_url ?? asset('images/placeholder.png') }}"
                                     alt="{{ $item->product?->name ?? 'Sản phẩm' }}"
                                     class="h-12 w-12 rounded-lg border-2 border-white object-cover shadow-sm">
                            @endforeach
                            @if ($extra > 0)
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg border-2 border-white bg-primary-light text-xs font-semibold text-primary shadow-sm">
                                    +{{ $extra }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-sm font-semibold text-neutral-text">
                                    #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>
                            <p class="mt-0.5 truncate text-sm text-neutral-muted">
                                {{ $order->items->map(fn($i) => $i->product?->name ?? 'Sản phẩm')->join(', ') }}
                            </p>
                            <p class="mt-0.5 text-xs text-neutral-muted">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                                · {{ $order->items->sum('quantity') }} sản phẩm
                            </p>
                        </div>

                        {{-- Total + arrow --}}
                        <div class="flex flex-shrink-0 items-center gap-2">
                            <span class="text-sm font-bold text-primary-dark">
                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </span>
                            <svg class="h-4 w-4 text-neutral-muted group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>

                    </a>

                    {{-- Dải đánh giá — chỉ hiện với đơn completed --}}
                    @if ($order->status === 'completed')
                        <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                            <p class="mb-2 text-xs font-medium text-neutral-muted uppercase tracking-wide">Đánh giá sản phẩm</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($order->items as $item)
                                    @if ($item->product)
                                        @php $reviewed = isset($reviewedProductIds[$item->product_id]); @endphp
                                        @if ($reviewed)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 border border-green-200 px-3 py-1 text-xs font-medium text-green-700">
                                                <span class="flex items-center gap-0.5">
                                                    @for ($s = 1; $s <= 5; $s++)
                                                        <svg class="h-3 w-3 {{ $s <= $reviewedProductIds[$item->product_id] ? 'text-warning' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endfor
                                                </span>
                                                <span class="max-w-[120px] truncate">{{ $item->product->name }}</span>
                                            </span>
                                        @else
                                            <a href="{{ route('products.show', $item->product) }}"
                                               class="inline-flex items-center gap-1.5 rounded-full bg-white border border-gray-200 px-3 py-1 text-xs font-medium text-neutral-text hover:border-primary hover:text-primary transition-colors">
                                                <svg class="h-3 w-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                                </svg>
                                                Đánh giá {{ $item->product->name }}
                                            </a>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        @if ($orders->hasPages())
            <div class="mt-8">
                {{ $orders->onEachSide(1)->links() }}
            </div>
        @endif
    @endif

</x-account-layout>
