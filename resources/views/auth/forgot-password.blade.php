<x-app-layout title="Quên mật khẩu — AnimeShop">
    <x-container class="py-12">
        <div class="mx-auto max-w-md">
            <div class="rounded-xl bg-white p-8 shadow-sm">

                {{-- Icon --}}
                <div class="mb-5 flex justify-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-light">
                        <svg class="h-7 w-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                        </svg>
                    </div>
                </div>

                <h1 class="mb-2 text-center text-2xl font-bold text-neutral-text">Quên mật khẩu?</h1>
                <p class="mb-6 text-center text-sm text-neutral-muted">
                    Nhập email tài khoản, chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.
                </p>

                {{-- Thông báo gửi thành công --}}
                @if (session('status'))
                    <div class="mb-5 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-neutral-text">Email</label>
                        <x-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <x-button type="submit" variant="primary" class="w-full justify-center">
                        Gửi link đặt lại mật khẩu
                    </x-button>
                </form>

                <p class="mt-5 text-center text-sm text-neutral-muted">
                    Nhớ mật khẩu rồi?
                    <a href="{{ route('login') }}" class="font-medium text-primary transition-colors hover:text-primary-dark">Đăng nhập</a>
                </p>

            </div>
        </div>
    </x-container>
</x-app-layout>
