<div>

    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-neutral-text">Địa chỉ đã lưu</h2>
        <x-button wire:click="openCreate" variant="primary" size="sm">
            <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Thêm địa chỉ mới
        </x-button>
    </div>

    {{-- Form thêm / sửa địa chỉ --}}
    @if ($showForm)
        <div class="mb-6 rounded-xl bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 class="text-sm font-semibold text-neutral-text">
                    {{ $editingId ? 'Sửa địa chỉ' : 'Thêm địa chỉ mới' }}
                </h3>
                <button
                    type="button"
                    wire:click="cancel"
                    class="rounded-lg p-1 text-neutral-muted hover:text-neutral-text hover:bg-gray-100 transition-colors"
                    aria-label="Đóng"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-4">

                {{-- Nhãn --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Nhãn địa chỉ
                    </label>
                    <x-input wire:model="label" type="text" placeholder="Nhà, Cơ quan, ..." />
                    @error('label')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tên người nhận --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                            Tên người nhận <span class="text-secondary">*</span>
                        </label>
                        <x-input wire:model="recipientName" type="text" placeholder="Nguyễn Văn A" required />
                        @error('recipientName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Số điện thoại --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                            Số điện thoại <span class="text-secondary">*</span>
                        </label>
                        <x-input wire:model="phone" type="tel" placeholder="0912 345 678" required />
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Địa chỉ --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-neutral-text">
                        Địa chỉ <span class="text-secondary">*</span>
                    </label>
                    <textarea
                        wire:model="address"
                        rows="3"
                        placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-neutral-text placeholder-neutral-muted transition-colors focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 resize-none"
                    ></textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <x-button wire:click="save" wire:loading.attr="disabled" variant="primary">
                        <svg wire:loading wire:target="save" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Lưu địa chỉ
                    </x-button>
                    <x-button wire:click="cancel" variant="secondary" size="base">
                        Hủy
                    </x-button>
                </div>

            </div>
        </div>
    @endif

    {{-- Danh sách địa chỉ --}}
    @if ($addresses->isEmpty())
        <div class="rounded-xl bg-white py-20 px-10 text-center shadow-sm">
            <svg class="mx-auto mb-4 h-14 w-14 text-neutral-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
            </svg>
            <p class="text-sm text-neutral-muted">Bạn chưa có địa chỉ nào được lưu.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($addresses as $addr)
                <div class="rounded-xl bg-white shadow-sm overflow-hidden">
                    <div class="flex items-start gap-4 p-4">

                        {{-- Icon --}}
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light">
                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                {{-- Label badge --}}
                                @if ($addr->label)
                                    <span class="inline-flex items-center rounded-md bg-neutral-bg px-2 py-0.5 text-xs font-medium text-neutral-text">
                                        {{ $addr->label }}
                                    </span>
                                @endif
                                {{-- Default badge --}}
                                @if ($addr->is_default)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-primary-light px-2 py-0.5 text-xs font-semibold text-primary">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Mặc định
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm font-semibold text-neutral-text">{{ $addr->recipient_name }}</p>
                            <p class="text-sm text-neutral-muted">{{ $addr->phone }}</p>
                            <p class="mt-0.5 text-sm text-neutral-text">{{ $addr->address }}</p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex shrink-0 flex-col items-end gap-1.5">
                            @if (!$addr->is_default)
                                <button
                                    type="button"
                                    wire:click="setDefault({{ $addr->id }})"
                                    class="text-xs text-neutral-muted hover:text-primary transition-colors"
                                >
                                    Đặt mặc định
                                </button>
                            @endif
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    wire:click="openEdit({{ $addr->id }})"
                                    class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-neutral-text border border-gray-200 hover:border-primary hover:text-primary transition-colors"
                                >
                                    Sửa
                                </button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $addr->id }})"
                                    wire:confirm="Bạn có chắc muốn xóa địa chỉ này?"
                                    class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 border border-red-100 hover:bg-red-50 transition-colors"
                                >
                                    Xóa
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
