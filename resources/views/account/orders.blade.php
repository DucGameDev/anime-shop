<x-app-layout title="Đơn hàng của tôi — AnimeShop">

    <x-container class="py-8 lg:py-16">

        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-text">Đơn hàng của tôi</h1>
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

                    <a href="{{ route('orders.show', $order) }}"
                       class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm hover:shadow-md transition-shadow group">

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
                @endforeach
            </div>

            @if ($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->onEachSide(1)->links() }}
                </div>
            @endif
        @endif

    </x-container>

</x-app-layout>
