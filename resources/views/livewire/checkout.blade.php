@push('head')
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
@endif
@endpush

<div>
    @if (empty($items))
        {{-- Giỏ trống --}}
        <div class="flex flex-col items-center justify-center py-20 text-center gap-4">
            <p class="text-neutral-muted">Giỏ hàng trống, không thể thanh toán.</p>
            <x-button variant="primary">
                <a href="{{ route('cart.index') }}">Quay lại giỏ hàng</a>
            </x-button>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ===== CỘT TRÁI: FORM ===== --}}
            <div class="lg:w-2/3">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-semibold text-neutral-text">Thông tin đặt hàng</h2>

                    <div class="space-y-4">

                        {{-- Honeypot: ẩn với CSS, bot sẽ điền vào --}}
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

                        {{-- Địa chỉ --}}
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
                                rows="2"
                                placeholder="Yêu cầu đặc biệt, giờ giao hàng, ghi chú cho người bán..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-neutral-text placeholder-neutral-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 resize-y max-h-[8rem]"
                            ></textarea>
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
                <div class="rounded-lg border border-gray-200 bg-white p-5 lg:sticky lg:top-20">
                    <h2 class="mb-4 text-base font-semibold text-neutral-text">Tóm tắt đơn hàng</h2>

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

                    <div class="my-4 border-t border-dashed border-gray-200"></div>

                    <div class="flex items-baseline justify-between">
                        <span class="text-sm font-medium text-neutral-text">Tổng cộng</span>
                        <span class="text-xl font-bold text-primary-dark tabular-nums">
                            {{ number_format($total, 0, ',', '.') }}₫
                        </span>
                    </div>

                    <p class="mt-1 text-xs text-neutral-muted">Chưa bao gồm phí vận chuyển</p>

                    {{-- Nút đặt hàng (desktop) --}}
                    <div class="mt-5 hidden lg:block" x-data>
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
