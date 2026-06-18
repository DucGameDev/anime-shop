<x-app-layout title="Đăng ký — AnimeShop">
    <x-container class="py-12">
        <div class="mx-auto max-w-md">
            <div class="rounded-xl bg-white p-8 shadow-sm">

                <h1 class="mb-6 text-center text-2xl font-bold text-neutral-text">Tạo tài khoản</h1>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-neutral-text mb-1">Họ tên</label>
                        <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nguyễn Văn A" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-neutral-text mb-1">Email</label>
                        <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-neutral-text mb-1">Mật khẩu</label>
                        <x-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Tối thiểu 8 ký tự" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-neutral-text mb-1">Xác nhận mật khẩu</label>
                        <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <x-button type="submit" variant="primary" class="w-full justify-center">
                        Đăng ký
                    </x-button>

                    <p class="text-center text-sm text-neutral-muted">
                        Đã có tài khoản?
                        <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Đăng nhập</a>
                    </p>
                </form>

            </div>
        </div>
    </x-container>
</x-app-layout>
