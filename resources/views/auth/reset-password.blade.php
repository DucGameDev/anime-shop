<x-app-layout title="Đặt lại mật khẩu — AnimeShop">
    <x-container class="py-12">
        <div class="mx-auto max-w-md">
            <div class="rounded-xl bg-white p-8 shadow-sm">

                {{-- Icon --}}
                <div class="mb-5 flex justify-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-light">
                        <svg class="h-7 w-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z"/>
                        </svg>
                    </div>
                </div>

                <h1 class="mb-2 text-center text-2xl font-bold text-neutral-text">Đặt lại mật khẩu</h1>
                <p class="mb-6 text-center text-sm text-neutral-muted">Nhập mật khẩu mới cho tài khoản của bạn.</p>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-neutral-text">Email</label>
                        <x-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="you@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-neutral-text">Mật khẩu mới</label>
                        <x-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-neutral-text">Xác nhận mật khẩu</label>
                        <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <x-button type="submit" variant="primary" class="w-full justify-center">
                        Đặt lại mật khẩu
                    </x-button>
                </form>

            </div>
        </div>
    </x-container>
</x-app-layout>
