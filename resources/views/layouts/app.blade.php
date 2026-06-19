<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?: config('app.name', 'AnimeShop') }}</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Open Graph / link preview --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'AnimeShop') }}">
    <meta property="og:title" content="{{ $title ?: config('app.name', 'AnimeShop') }}">
    <meta property="og:description" content="Thiên đường đồ anime — figure, áo, manga, sticker chính hãng. Đồng hành cùng mọi otaku!">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-image.png">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ config('app.url') }}/images/og-image.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet">

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-sans antialiased bg-neutral-bg text-neutral-text text-[17px]">

    {{-- ===== HEADER ===== --}}
    <header class="bg-white shadow-sm sticky top-0 z-50" x-data="{ open: false }">
        <x-container>
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <span class="text-2xl font-bold text-primary-dark tracking-tight">
                        Anime<span class="text-primary">Shop</span>
                    </span>
                </a>

                {{-- Nav — desktop (hidden on mobile) --}}
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}"
                       class="text-lg font-medium text-neutral-text hover:text-primary transition-colors">
                        Trang chủ
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="text-lg font-medium text-neutral-text hover:text-primary transition-colors">
                        Sản phẩm
                    </a>
                </nav>

                {{-- Right side: account + cart icon + hamburger --}}
                <div class="flex items-center gap-3">

                    {{-- Account --}}
                    @auth
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open"
                                    class="flex items-center gap-1.5 text-base font-medium text-neutral-text hover:text-primary transition-colors">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-light text-primary font-semibold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <svg class="h-3.5 w-3.5 text-neutral-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 top-10 z-50 w-48 rounded-xl bg-white py-1 shadow-lg ring-1 ring-black/5"
                                 style="display:none">
                                <div class="border-b border-gray-100 px-4 py-2">
                                    <p class="text-xs font-semibold text-neutral-text truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-neutral-muted truncate">{{ auth()->user()->email }}</p>
                                </div>
                                @php
                                    $menuItems = [
                                        ['route' => 'account.orders',    'label' => 'Đơn hàng',       'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
                                        ['route' => 'account.profile',   'label' => 'Thông tin',       'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                                        ['route' => 'account.addresses', 'label' => 'Sổ địa chỉ',     'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                                        ['route' => 'account.favorites', 'label' => 'Yêu thích',      'icon' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z'],
                                    ];
                                @endphp

                                @foreach ($menuItems as $item)
                                    <a href="{{ route($item['route']) }}"
                                       class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-text hover:bg-primary-light hover:text-primary transition-colors
                                           {{ request()->routeIs($item['route']) ? 'text-primary bg-primary-light/60' : '' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                        </svg>
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach

                                <div class="my-1 border-t border-gray-100"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                        </svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="hidden lg:inline-flex text-base font-medium text-neutral-text hover:text-primary transition-colors">
                            Đăng nhập
                        </a>
                    @endauth

                    {{-- Cart icon — Livewire component tự cập nhật badge --}}
                    <livewire:cart-icon />

                    {{-- Hamburger — mobile only --}}
                    <button @click="open = !open"
                            class="lg:hidden p-2 rounded-lg text-neutral-text hover:text-primary hover:bg-primary-light transition-colors"
                            :aria-expanded="open.toString()"
                            aria-label="Toggle menu">
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden border-t border-gray-100 py-3 space-y-1"
                 style="display:none">
                <a href="{{ route('home') }}"
                   class="block px-3 py-2.5 rounded-lg text-base font-medium text-neutral-text hover:text-primary hover:bg-primary-light transition-colors">
                    Trang chủ
                </a>
                <a href="{{ route('products.index') }}"
                   class="block px-3 py-2.5 rounded-lg text-base font-medium text-neutral-text hover:text-primary hover:bg-primary-light transition-colors">
                    Sản phẩm
                </a>
                @auth
                    <a href="{{ route('account.orders') }}"
                       class="block px-3 py-2.5 rounded-lg text-base font-medium text-neutral-text hover:text-primary hover:bg-primary-light transition-colors">
                        Đơn hàng của tôi
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-3 py-2.5 rounded-lg text-base font-medium text-red-600 hover:bg-red-50 transition-colors">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="block px-3 py-2.5 rounded-lg text-base font-medium text-primary hover:bg-primary-light transition-colors">
                        Đăng nhập
                    </a>
                @endauth
            </div>
        </x-container>
    </header>

    {{-- ===== MAIN CONTENT ===== --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-neutral-bg border-t border-gray-200 mt-16">
        <x-container>
            <div class="py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- Thương hiệu --}}
                <div>
                    <span class="text-lg font-bold text-primary-dark">
                        Anime<span class="text-primary">Shop</span>
                    </span>
                    <p class="mt-2 text-sm text-neutral-muted leading-relaxed">
                        Thiên đường đồ anime — figure, áo, manga, sticker chính hãng. Giao hàng toàn quốc, đổi trả 7 ngày.
                    </p>
                </div>

                {{-- Hỗ trợ --}}
                <div>
                    <h3 class="text-sm font-semibold text-neutral-text uppercase tracking-wide mb-3">
                        Hỗ trợ
                    </h3>
                    <ul class="space-y-2 text-sm text-neutral-muted">
                        <li>
                            <a href="{{ route('static.order-guide') }}" class="hover:text-primary transition-colors">
                                Hướng dẫn đặt hàng
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('static.payment') }}" class="hover:text-primary transition-colors">
                                Hình thức thanh toán
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('static.shipping') }}" class="hover:text-primary transition-colors">
                                Chính sách vận chuyển
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('static.returns') }}" class="hover:text-primary transition-colors">
                                Chính sách đổi trả
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Liên hệ --}}
                <div>
                    <h3 class="text-sm font-semibold text-neutral-text uppercase tracking-wide mb-3">
                        Liên hệ
                    </h3>
                    <ul class="space-y-2 text-sm text-neutral-muted">
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            <a href="mailto:ducdev.work@gmail.com" class="hover:text-primary transition-colors">
                                ducdev.work@gmail.com
                            </a>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253M3 12c0 .778.099 1.533.284 2.253"/>
                            </svg>
                            <a href="https://ducdev.work" target="_blank" class="hover:text-primary transition-colors">
                                ducdev.work
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Google Maps --}}
                <div>
                    <h3 class="text-sm font-semibold text-neutral-text uppercase tracking-wide mb-3">
                        Tìm chúng tôi
                    </h3>
                    <div class="overflow-hidden rounded-lg">
                        <iframe
                            src="https://maps.google.com/maps?q=Akihabara+Electric+Town,+Tokyo,+Japan&z=15&output=embed"
                            width="100%"
                            height="140"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Akihabara Electric Town"
                        ></iframe>
                    </div>
                </div>

            </div>

            <div class="border-t border-gray-200 py-5 text-center text-xs text-neutral-muted">
                © {{ date('Y') }} AnimeShop. All rights reserved.
            </div>
        </x-container>
    </footer>

    {{-- ===== CHAT WIDGET ===== --}}
    <x-chat-widget />

    {{-- ===== TOAST NOTIFICATION ===== --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:show-toast.window="
            message = $event.detail.message;
            show = true;
            clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(() => show = false, 2000)
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 left-1/2 z-[100] -translate-x-1/2 flex items-center gap-2 rounded-lg bg-neutral-text px-4 py-2.5 text-sm text-white shadow-lg"
        style="display:none"
        role="status"
        aria-live="polite"
    >
        <svg class="h-4 w-4 flex-shrink-0 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
        </svg>
        <span x-text="message"></span>
    </div>

    @if (session('flash_toast'))
    <script>
        document.addEventListener('alpine:init', () => {}, { once: true });
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => window.dispatchEvent(new CustomEvent('show-toast', {
                detail: { message: @js(session('flash_toast')) }
            })), 300);
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>
