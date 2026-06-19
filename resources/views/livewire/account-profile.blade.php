<div x-data="{ tab: '{{ $activeTab }}' }">

    {{-- Card tiêu đề + tab switcher --}}
    <div class="rounded-xl bg-white shadow-sm overflow-hidden">

        {{-- Tab header --}}
        <div class="flex border-b border-gray-100">
            <button
                type="button"
                @click="tab = 'info'; $wire.set('activeTab', 'info')"
                class="flex-1 px-5 py-3.5 text-sm font-medium transition-colors focus:outline-none"
                :class="tab === 'info'
                    ? 'text-primary border-b-2 border-primary bg-primary-light/40'
                    : 'text-neutral-muted hover:text-neutral-text hover:bg-gray-50'"
            >
                Thông tin cá nhân
            </button>
            <button
                type="button"
                @click="tab = 'password'; $wire.set('activeTab', 'password')"
                class="flex-1 px-5 py-3.5 text-sm font-medium transition-colors focus:outline-none"
                :class="tab === 'password'
                    ? 'text-primary border-b-2 border-primary bg-primary-light/40'
                    : 'text-neutral-muted hover:text-neutral-text hover:bg-gray-50'"
            >
                Đổi mật khẩu
            </button>
        </div>

        {{-- Tab 1: Thông tin cá nhân --}}
        <div x-show="tab === 'info'" x-transition class="p-6">

<div class="space-y-4 max-w-md">
                {{-- Họ tên --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Họ tên <span class="text-secondary">*</span>
                    </label>
                    <x-input wire:model="name" type="text" placeholder="Nguyễn Văn A" required />
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Email <span class="text-secondary">*</span>
                    </label>
                    <x-input wire:model="email" type="email" placeholder="example@email.com" required />
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Số điện thoại --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Số điện thoại
                        <span class="ml-1 text-xs text-neutral-muted font-normal">(tùy chọn)</span>
                    </label>
                    <x-input wire:model="phone" type="tel" placeholder="0912 345 678" />
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <x-button wire:click="saveInfo" wire:loading.attr="disabled" variant="primary">
                        <svg wire:loading wire:target="saveInfo" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Lưu thay đổi
                    </x-button>
                </div>
            </div>
        </div>

        {{-- Tab 2: Đổi mật khẩu --}}
        <div x-show="tab === 'password'" x-transition class="p-6">

            <div class="space-y-4 max-w-md">
                {{-- Mật khẩu hiện tại --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Mật khẩu hiện tại <span class="text-secondary">*</span>
                    </label>
                    <x-input wire:model="currentPassword" type="password" placeholder="••••••••" autocomplete="current-password" />
                    @error('currentPassword')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mật khẩu mới --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Mật khẩu mới <span class="text-secondary">*</span>
                    </label>
                    <x-input wire:model="newPassword" type="password" placeholder="••••••••" autocomplete="new-password" />
                    <p class="mt-1 text-xs text-neutral-muted">Tối thiểu 8 ký tự.</p>
                    @error('newPassword')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Xác nhận mật khẩu mới --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Xác nhận mật khẩu mới <span class="text-secondary">*</span>
                    </label>
                    <x-input wire:model="newPasswordConfirmation" type="password" placeholder="••••••••" autocomplete="new-password" />
                    @error('newPasswordConfirmation')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <x-button wire:click="savePassword" wire:loading.attr="disabled" variant="primary">
                        <svg wire:loading wire:target="savePassword" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Đổi mật khẩu
                    </x-button>
                </div>
            </div>
        </div>

    </div>
</div>
