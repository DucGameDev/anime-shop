<x-app-layout title="Đặt hàng thành công — AnimeShop">

    <x-container class="py-8 lg:py-16">
        <div class="mx-auto max-w-2xl">
            <div class="rounded-lg bg-white p-8 shadow-sm">

                {{-- Icon check tròn --}}
                <div class="mb-5 flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-secondary-light">
                        <svg class="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-neutral-text">Đặt hàng thành công!</h1>
                    <p class="mt-1 text-sm text-neutral-muted">
                        Cảm ơn bạn đã mua hàng tại AnimeShop. Chúng tôi sẽ liên hệ sớm nhất!
                    </p>
                </div>

                {{-- Mã đơn hàng + badge trạng thái --}}
                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                    <span class="text-sm text-neutral-muted">Mã đơn hàng:</span>
                    <span class="font-mono text-base font-semibold text-neutral-text">
                        #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-warning-light px-2.5 py-0.5 text-xs font-semibold text-warning">
                        Đang xử lý
                    </span>
                </div>

                <div class="my-6 border-t border-dashed border-gray-200"></div>

                {{-- Chi tiết đơn hàng --}}
                <div>
                    <h2 class="mb-3 text-sm font-semibold text-neutral-text">Chi tiết đơn hàng</h2>
                    <div class="space-y-2">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-muted">
                                    {{ $item->product->name ?? 'Sản phẩm' }} × {{ $item->quantity }}
                                </span>
                                <span class="tabular-nums text-neutral-text">
                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex justify-between border-t border-gray-200 pt-3">
                        <span class="text-sm font-semibold text-neutral-text">Tổng cộng</span>
                        <span class="text-base font-bold text-primary-dark tabular-nums">
                            {{ number_format($order->total_amount, 0, ',', '.') }}₫
                        </span>
                    </div>
                </div>

                <div class="my-6 border-t border-dashed border-gray-200"></div>

                {{-- Thông tin giao hàng --}}
                <div class="space-y-1.5 text-sm">
                    <h2 class="mb-2 font-semibold text-neutral-text">Thông tin giao hàng</h2>
                    <p>
                        <span class="font-medium text-neutral-text">Người nhận:</span>
                        <span class="text-neutral-muted"> {{ $order->customer_name }}</span>
                    </p>
                    <p>
                        <span class="font-medium text-neutral-text">Điện thoại:</span>
                        <span class="text-neutral-muted"> {{ $order->phone }}</span>
                    </p>
                    <p>
                        <span class="font-medium text-neutral-text">Địa chỉ:</span>
                        <span class="text-neutral-muted"> {{ $order->address }}</span>
                    </p>
                </div>

                {{-- CTA --}}
                <div class="mt-8 text-center">
                    <x-button variant="primary">
                        <a href="{{ route('products.index') }}" class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                            </svg>
                            Tiếp tục mua sắm
                        </a>
                    </x-button>
                </div>

            </div>
        </div>
    </x-container>

</x-app-layout>
