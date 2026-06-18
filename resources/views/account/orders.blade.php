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
            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $statusConfig = match($order->status) {
                            'pending'   => ['label' => 'Chờ xử lý',    'class' => 'bg-warning-light text-warning'],
                            'shipped'   => ['label' => 'Đang giao',     'class' => 'bg-info-light text-info'],
                            'completed' => ['label' => 'Hoàn thành',    'class' => 'bg-green-100 text-green-700'],
                            'cancelled' => ['label' => 'Đã huỷ',        'class' => 'bg-red-100 text-red-600'],
                            default     => ['label' => $order->status,  'class' => 'bg-gray-100 text-gray-600'],
                        };
                    @endphp

                    <a href="{{ route('orders.show', $order) }}"
                       class="block rounded-xl bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-sm font-semibold text-neutral-text">
                                    #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-neutral-muted">
                                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                <span class="font-bold text-primary-dark">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                </span>
                            </div>
                        </div>

                        @if ($order->items->isNotEmpty())
                            <p class="mt-2 text-sm text-neutral-muted line-clamp-1">
                                {{ $order->items->map(fn($i) => $i->product->name ?? 'Sản phẩm')->join(', ') }}
                            </p>
                        @endif
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
