@push('head')
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
@endif
@endpush

<div>
    @if (empty($items))
        <div class="flex flex-col items-center justify-center py-20 text-center gap-4">
            <p class="text-neutral-muted">Giỏ hàng trống, không thể thanh toán.</p>
            <x-button variant="primary">
                <a href="{{ route('cart.index') }}">Quay lại giỏ hàng</a>
            </x-button>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ===== CỘT TRÁI: FORM ===== --}}
            <div class="lg:w-2/3 space-y-4">

                {{-- Chọn địa chỉ đã lưu (chỉ hiện khi đăng nhập và có địa chỉ) --}}
                @if ($isLoggedIn && $addresses->isNotEmpty())
                    <div class="rounded-lg bg-white p-5 shadow-sm">
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="text-base font-semibold text-neutral-text">Địa chỉ giao hàng</h2>
                            <a href="{{ route('account.addresses') }}" target="_blank"
                               class="text-xs text-primary hover:text-primary-dark transition-colors">
                                Quản lý địa chỉ
                            </a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($addresses as $addr)
                                @php $active = $selectedAddressId === $addr->id; @endphp
                                <button
                                    type="button"
                                    wire:click="selectAddress({{ $addr->id }})"
                                    class="group w-full rounded-xl border-2 p-4 text-left transition-all
                                        {{ $active
                                            ? 'border-primary shadow-sm shadow-primary/10'
                                            : 'border-gray-200 hover:border-gray-300 hover:shadow-sm' }}"
                                >
                                    {{-- Badges + radio dot cùng hàng --}}
                                    <div class="mb-2 flex items-center justify-between gap-2">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @if ($addr->label)
                                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium
                                                    {{ $active ? 'bg-primary text-white' : 'bg-gray-100 text-neutral-muted' }}">
                                                    {{ $addr->label }}
                                                </span>
                                            @endif
                                            @if ($addr->is_default)
                                                <span class="inline-flex items-center gap-1 rounded-md bg-primary-light px-2 py-0.5 text-xs font-medium text-primary">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Mặc định
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Radio dot --}}
                                        <div class="shrink-0 flex h-4 w-4 items-center justify-center rounded-full border-2 transition-colors
                                            {{ $active ? 'border-primary bg-primary' : 'border-gray-300 group-hover:border-gray-400' }}">
                                            @if ($active)
                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-sm font-semibold text-neutral-text">{{ $addr->recipient_name }}</p>
                                    <p class="mt-0.5 text-xs text-neutral-muted">{{ $addr->phone }}</p>
                                    <p class="mt-1 text-xs text-neutral-text line-clamp-2 leading-relaxed">{{ $addr->address }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Form thông tin --}}
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-semibold text-neutral-text">Thông tin đặt hàng</h2>

                    <div class="space-y-4">

                        {{-- Honeypot --}}
                        <div style="position:absolute;left:-9999px;top:-9999px;opacity:0;" aria-hidden="true" tabindex="-1">
                            <input wire:model="website" type="text" name="website" autocomplete="off" tabindex="-1" />
                        </div>

                        {{-- Họ và tên --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-text">
                                Họ và tên <span class="text-red-500">*</span>
                            </label>
                            @if($isLoggedIn)
                                <p class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-neutral-text">{{ $customerName }}</p>
                            @else
                                <x-input wire:model="customerName" type="text" placeholder="Nguyễn Văn A" />
                            @endif
                            @error('customerName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-text">
                                Email <span class="text-red-500">*</span>
                            </label>
                            @if($isLoggedIn)
                                <p class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-neutral-text">{{ $email }}</p>
                            @else
                                <x-input wire:model="email" type="email" placeholder="example@email.com" />
                            @endif
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Số điện thoại --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-text">
                                Số điện thoại <span class="text-red-500">*</span>
                            </label>
                            <x-input wire:model="phone" type="tel" placeholder="0912345678" />
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Địa chỉ giao hàng --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-text">
                                Địa chỉ giao hàng <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model="address"
                                rows="3"
                                placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-neutral-text placeholder-neutral-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 resize-none"
                            ></textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ghi chú --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-text">
                                Ghi chú <span class="text-xs text-neutral-muted font-normal">(không bắt buộc)</span>
                            </label>
                            <textarea
                                wire:model="note"
                                rows="3"
                                placeholder="Yêu cầu đặc biệt, giờ giao hàng, ghi chú cho người bán..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-neutral-text placeholder-neutral-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 resize-y max-h-[8rem]"
                            ></textarea>
                        </div>

                        {{-- Phương thức thanh toán --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-neutral-text">Phương thức thanh toán</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border-2 p-4 transition-colors
                                    {{ $paymentMethod === 'bank_transfer' ? 'border-primary bg-primary-light' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                    <input type="radio" wire:model.live="paymentMethod" value="bank_transfer" class="sr-only" />
                                    <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 mt-0.5
                                        {{ $paymentMethod === 'bank_transfer' ? 'border-primary' : 'border-gray-300' }}">
                                        @if ($paymentMethod === 'bank_transfer')
                                            <div class="h-2.5 w-2.5 rounded-full bg-primary"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-text">Chuyển khoản ngân hàng</p>
                                        <p class="mt-0.5 text-xs text-neutral-muted">Quét mã QR hoặc chuyển khoản thủ công</p>
                                    </div>
                                </label>

                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border-2 p-4 transition-colors
                                    {{ $paymentMethod === 'cod' ? 'border-primary bg-primary-light' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                    <input type="radio" wire:model.live="paymentMethod" value="cod" class="sr-only" />
                                    <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 mt-0.5
                                        {{ $paymentMethod === 'cod' ? 'border-primary' : 'border-gray-300' }}">
                                        @if ($paymentMethod === 'cod')
                                            <div class="h-2.5 w-2.5 rounded-full bg-primary"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-text">Thanh toán khi nhận hàng</p>
                                        <p class="mt-0.5 text-xs text-neutral-muted">Trả tiền mặt cho người giao hàng</p>
                                    </div>
                                </label>

                            </div>
                        </div>
                    </div>

                    {{-- Nút đặt hàng (mobile) --}}
                    <div class="mt-6 lg:hidden" x-data>
                        <button
                            @click="
                                @if(config('services.recaptcha.site_key'))
                                const token = await new Promise(resolve => grecaptcha.ready(() => grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'checkout'}).then(resolve)));
                                $wire.recaptchaToken = token;
                                @endif
                                $wire.placeOrder();
                            "
                            wire:loading.attr="disabled"
                            class="w-full min-h-[44px] inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <svg wire:loading wire:target="placeOrder" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="placeOrder">Đặt hàng</span>
                            <span wire:loading wire:target="placeOrder">Đang xử lý...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ===== CỘT PHẢI: TÓM TẮT ===== --}}
            <div class="lg:w-1/3">
                <div class="rounded-lg border border-gray-200 bg-white p-5 lg:sticky lg:top-20 space-y-4">
                    <h2 class="text-base font-semibold text-neutral-text">Tóm tắt đơn hàng</h2>

                    {{-- Danh sách sản phẩm --}}
                    <div class="space-y-2">
                        @foreach ($items as $item)
                            <div class="flex justify-between gap-2 text-sm">
                                <span class="truncate max-w-[160px] text-neutral-muted">
                                    {{ $item['name'] }} × {{ $item['quantity'] }}
                                </span>
                                <span class="flex-shrink-0 tabular-nums text-neutral-text">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-dashed border-gray-200"></div>

                    {{-- Mã giảm giá --}}
                    <div>
                        @if ($appliedVoucher)
                            <div class="flex items-center justify-between rounded-lg bg-green-50 border border-green-200 px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-green-700">{{ $appliedVoucher }}</span>
                                </div>
                                <button
                                    wire:click="removeVoucher"
                                    type="button"
                                    class="text-xs text-green-600 hover:text-green-800 transition-colors"
                                >
                                    Xóa
                                </button>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <x-input
                                    wire:model="voucherInput"
                                    type="text"
                                    placeholder="Nhập mã giảm giá"
                                    class="flex-1 uppercase"
                                    wire:keydown.enter="applyVoucher"
                                />
                                <button
                                    wire:click="applyVoucher"
                                    wire:loading.attr="disabled"
                                    wire:target="applyVoucher"
                                    type="button"
                                    class="shrink-0 rounded-lg border border-primary px-3 py-2 text-sm font-medium text-primary hover:bg-primary-light transition-colors disabled:opacity-60"
                                >
                                    Áp dụng
                                </button>
                            </div>
                            @if ($voucherError)
                                <p class="mt-1.5 text-xs text-red-600">{{ $voucherError }}</p>
                            @endif
                        @endif
                    </div>

                    <div class="border-t border-dashed border-gray-200"></div>

                    {{-- Tổng tiền --}}
                    <div class="space-y-1.5">
                        <div class="flex items-baseline justify-between text-sm">
                            <span class="text-neutral-muted">Tạm tính</span>
                            <span class="tabular-nums text-neutral-text">{{ number_format($total, 0, ',', '.') }}₫</span>
                        </div>

                        @if ($discountAmount > 0)
                            <div class="flex items-baseline justify-between text-sm">
                                <span class="text-green-600">Giảm giá ({{ $appliedVoucher }})</span>
                                <span class="tabular-nums text-green-600">-{{ number_format($discountAmount, 0, ',', '.') }}₫</span>
                            </div>
                        @endif

                        <div class="flex items-baseline justify-between pt-1 border-t border-gray-100">
                            <span class="text-sm font-medium text-neutral-text">Tổng cộng</span>
                            <span class="text-xl font-bold text-primary-dark tabular-nums">
                                {{ number_format($finalTotal, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-neutral-muted">Chưa bao gồm phí vận chuyển</p>

                    {{-- Nút đặt hàng (desktop) --}}
                    <div class="hidden lg:block" x-data>
                        <button
                            @click="
                                @if(config('services.recaptcha.site_key'))
                                const token = await new Promise(resolve => grecaptcha.ready(() => grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'checkout'}).then(resolve)));
                                $wire.recaptchaToken = token;
                                @endif
                                $wire.placeOrder();
                            "
                            wire:loading.attr="disabled"
                            class="w-full min-h-[44px] inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <svg wire:loading wire:target="placeOrder" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="placeOrder">Đặt hàng ngay</span>
                            <span wire:loading wire:target="placeOrder">Đang xử lý...</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    @endif
</div>
