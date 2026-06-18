<x-app-layout title="Đăng nhập — AnimeShop">
    <x-container class="py-12">
        <div class="mx-auto max-w-md">
            <div class="rounded-xl bg-white p-8 shadow-sm">

                <h1 class="mb-6 text-center text-2xl font-bold text-neutral-text">Đăng nhập</h1>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if (session('admin_redirect'))
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        Tài khoản quản trị viên vui lòng đăng nhập tại
                        <a href="/admin/login" class="font-semibold underline hover:text-amber-900">/admin/login</a>.
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-neutral-text mb-1">Email</label>
                        <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-neutral-text mb-1">Mật khẩu</label>
                        <x-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-neutral-muted">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
                            Ghi nhớ đăng nhập
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-primary hover:text-primary-dark transition-colors">
                                Quên mật khẩu?
                            </a>
                        @endif
                    </div>

                    <x-button type="submit" variant="primary" class="w-full justify-center">
                        Đăng nhập
                    </x-button>

                    <p class="text-center text-sm text-neutral-muted">
                        Chưa có tài khoản?
                        <a href="{{ route('register') }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Đăng ký ngay</a>
                    </p>
                </form>

            </div>
        </div>
    </x-container>
</x-app-layout>
