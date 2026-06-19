<x-app-layout title="Chi tiết đơn hàng — AnimeShop">

    <x-container class="py-8 lg:py-16">
        <div class="mx-auto max-w-2xl">
            <div class="rounded-lg bg-white p-8 shadow-sm">

                @php
                    $statusConfig = match($order->status) {
                        'pending'   => ['label' => 'Chờ xử lý',  'badge' => 'bg-warning-light text-warning',         'icon' => 'text-warning',   'bg' => 'bg-warning-light'],
                        'shipped'   => ['label' => 'Đang giao',   'badge' => 'bg-blue-100 text-blue-700',             'icon' => 'text-info',      'bg' => 'bg-blue-50'],
                        'completed' => ['label' => 'Hoàn thành',  'badge' => 'bg-green-100 text-green-700',           'icon' => 'text-secondary', 'bg' => 'bg-secondary-light'],
                        'cancelled' => ['label' => 'Đã huỷ',      'badge' => 'bg-red-100 text-red-600',               'icon' => 'text-red-400',   'bg' => 'bg-red-50'],
                        default     => ['label' => $order->status, 'badge' => 'bg-gray-100 text-gray-600',            'icon' => 'text-neutral-muted', 'bg' => 'bg-gray-50'],
                    };
                @endphp

                {{-- Icon trạng thái --}}
                <div class="mb-5 flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full {{ $statusConfig['bg'] }}">
                        @if ($order->status === 'cancelled')
                            <svg class="h-8 w-8 {{ $statusConfig['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        @elseif ($order->status === 'shipped')
                            <svg class="h-8 w-8 {{ $statusConfig['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                            </svg>
                        @else
                            <svg class="h-8 w-8 {{ $statusConfig['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- Heading theo trạng thái --}}
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-neutral-text">
                        @if ($order->status === 'completed') Đơn hàng đã hoàn thành!
                        @elseif ($order->status === 'shipped') Đơn hàng đang được giao!
                        @elseif ($order->status === 'cancelled') Đơn hàng đã bị huỷ
                        @else Đặt hàng thành công!
                        @endif
                    </h1>
                    <p class="mt-1 text-sm text-neutral-muted">
                        @if ($order->status === 'completed') Cảm ơn bạn đã mua hàng. Hẹn gặp lại lần sau!
                        @elseif ($order->status === 'shipped') Đơn hàng đang trên đường đến tay bạn.
                        @elseif ($order->status === 'cancelled') Đơn hàng này đã bị huỷ. Liên hệ shop nếu có thắc mắc.
                        @else Cảm ơn bạn đã mua hàng tại AnimeShop. Chúng tôi sẽ liên hệ sớm nhất!
                        @endif
                    </p>
                </div>

                {{-- Mã đơn hàng + badge trạng thái --}}
                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                    <span class="text-sm text-neutral-muted">Mã đơn hàng:</span>
                    <span class="font-mono text-base font-semibold text-neutral-text">
                        #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusConfig['badge'] }}">
                        {{ $statusConfig['label'] }}
                    </span>
                </div>

                <div class="my-6 border-t border-dashed border-gray-200"></div>

                {{-- QR Thanh toán — chỉ hiện khi chưa thanh toán --}}
                @if ($order->status === 'unpaid')
                    @php
                        $bankId      = config('payment.bank_id');
                        $accountNo   = config('payment.account_no');
                        $accountName = config('payment.account_name');
                        $amount      = (int) $order->total_amount;
                        $orderRef    = 'DH' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
                        $qrUrl       = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png"
                                     . "?amount={$amount}"
                                     . "&addInfo=" . urlencode($orderRef)
                                     . "&accountName=" . urlencode($accountName);
                    @endphp

                    <div class="text-center">
                        <h2 class="mb-1 text-sm font-semibold text-neutral-text">Thanh toán chuyển khoản</h2>
                        <p class="mb-4 text-xs text-neutral-muted">Quét mã QR bằng app ngân hàng để thanh toán</p>

                        <div class="inline-block rounded-2xl border-2 border-primary-light p-3 shadow-sm">
                            <img src="{{ $qrUrl }}" alt="QR thanh toán" class="h-52 w-52 rounded-xl">
                        </div>

                        <div class="mt-4 space-y-1 text-sm">
                            <p><span class="text-neutral-muted">Ngân hàng:</span> <span class="font-semibold text-neutral-text">Vietcombank (VCB)</span></p>
                            <p><span class="text-neutral-muted">Số tài khoản:</span> <span class="font-semibold text-neutral-text font-mono">{{ $accountNo }}</span></p>
                            <p><span class="text-neutral-muted">Chủ tài khoản:</span> <span class="font-semibold text-neutral-text">{{ $accountName }}</span></p>
                            <p><span class="text-neutral-muted">Số tiền:</span> <span class="font-bold text-primary-dark">{{ number_format($amount, 0, ',', '.') }}₫</span></p>
                            <p><span class="text-neutral-muted">Nội dung CK:</span> <span class="font-semibold text-neutral-text font-mono">{{ $orderRef }}</span></p>
                        </div>

                        <p class="mt-3 text-xs text-neutral-muted">
                            Đơn hàng sẽ được xác nhận sau khi chúng tôi nhận được thanh toán.
                        </p>
                    </div>

                    <div class="my-6 border-t border-dashed border-gray-200"></div>
                @endif

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
                    <div class="mt-3 border-t border-gray-200 pt-3 space-y-1.5">
                        @if ($order->voucher_code)
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-muted">Tạm tính</span>
                                <span class="tabular-nums text-neutral-text">
                                    {{ number_format((float)$order->total_amount + (float)$order->discount_amount, 0, ',', '.') }}₫
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-green-600">
                                    Mã giảm giá
                                    <span class="font-mono font-semibold rounded bg-green-50 px-1">{{ $order->voucher_code }}</span>
                                </span>
                                <span class="tabular-nums text-green-600">
                                    -{{ number_format((float)$order->discount_amount, 0, ',', '.') }}₫
                                </span>
                            </div>
                            <div class="border-t border-dashed border-gray-200 pt-1.5"></div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-sm font-semibold text-neutral-text">Tổng cộng</span>
                            <span class="text-base font-bold text-primary-dark tabular-nums">
                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </span>
                        </div>
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
                    <p>
                        <span class="font-medium text-neutral-text">Thanh toán:</span>
                        <span class="text-neutral-muted">
                            {{ $order->payment_method === 'cod' ? 'Tiền mặt khi nhận hàng' : 'Chuyển khoản ngân hàng' }}
                        </span>
                    </p>
                    @if ($order->note)
                        <p>
                            <span class="font-medium text-neutral-text">Ghi chú:</span>
                            <span class="text-neutral-muted"> {{ $order->note }}</span>
                        </p>
                    @endif
                </div>

                {{-- Đánh giá sản phẩm — chỉ hiện với đơn completed --}}
                @if ($order->status === 'completed')
                    <div class="my-6 border-t border-dashed border-gray-200"></div>

                    <div>
                        <h2 class="mb-3 text-sm font-semibold text-neutral-text">Đánh giá sản phẩm</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($order->items as $item)
                                @if ($item->product)
                                    @php $reviewed = isset($reviewedProductIds[$item->product_id]); @endphp
                                    @if ($reviewed)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 border border-green-200 px-3 py-1.5 text-xs font-medium text-green-700">
                                            <span class="flex items-center gap-0.5">
                                                @for ($s = 1; $s <= 5; $s++)
                                                    <svg class="h-3 w-3 {{ $s <= $reviewedProductIds[$item->product_id] ? 'text-warning' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </span>
                                            <span class="max-w-[140px] truncate">{{ $item->product->name }}</span>
                                        </span>
                                    @else
                                        <a href="{{ route('products.show', $item->product) }}"
                                           class="inline-flex items-center gap-1.5 rounded-full bg-white border border-gray-200 px-3 py-1.5 text-xs font-medium text-neutral-text hover:border-primary hover:text-primary transition-colors">
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

                {{-- CTA --}}
                <div class="mt-8 text-center">
                    <x-button href="{{ route('products.index') }}" variant="primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                        </svg>
                        Tiếp tục mua sắm
                    </x-button>
                </div>

            </div>
        </div>
    </x-container>

</x-app-layout>
