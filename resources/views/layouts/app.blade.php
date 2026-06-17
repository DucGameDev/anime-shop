<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteTitle   = config('app.name', 'AnimeShop');
        $pageTitle   = $title ? "$title | $siteTitle" : $siteTitle;
        $defaultDesc = 'Thiên đường đồ anime chính hãng tại Việt Nam — figure, áo, manga, sticker. Giao hàng toàn quốc, đổi trả 7 ngày.';
        $metaDesc    = $description ?: $defaultDesc;
        $canonical   = url()->current();
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if ($noindex)
    <meta name="robots" content="noindex,nofollow">
    @endif

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Open Graph / link preview --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteTitle }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image" content="{{ config('app.url') }}/og-image.png">
    <meta property="og:url" content="{{ $canonical }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ config('app.url') }}/og-image.png">

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $siteTitle }}",
        "url": "{{ config('app.url') }}",
        "logo": "{{ config('app.url') }}/og-image.png",
        "description": "{{ $defaultDesc }}"
    }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @stack('head')
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-bg text-neutral-text">

    {{-- ===== HEADER ===== --}}
    <header class="bg-white shadow-sm sticky top-0 z-50" x-data="{ open: false }">
        <x-container>
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <span class="text-xl font-bold text-primary-dark tracking-tight">
                        Anime<span class="text-primary">Shop</span>
                    </span>
                </a>

                {{-- Nav — desktop (hidden on mobile) --}}
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}"
                       class="text-sm font-medium text-neutral-text hover:text-primary transition-colors">
                        Trang chủ
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="text-sm font-medium text-neutral-text hover:text-primary transition-colors">
                        Sản phẩm
                    </a>
                    <a href="{{ route('cart.index') }}"
                       class="text-sm font-medium text-neutral-text hover:text-primary transition-colors">
                        Giỏ hàng
                    </a>
                </nav>

                {{-- Right side: cart icon + hamburger --}}
                <div class="flex items-center gap-3">

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
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-text hover:text-primary hover:bg-primary-light transition-colors">
                    Trang chủ
                </a>
                <a href="{{ route('products.index') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-text hover:text-primary hover:bg-primary-light transition-colors">
                    Sản phẩm
                </a>
                <a href="{{ route('cart.index') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-text hover:text-primary hover:bg-primary-light transition-colors">
                    Giỏ hàng
                </a>
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
            <div class="py-10 grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Thương hiệu --}}
                <div>
                    <span class="text-lg font-bold text-primary-dark">
                        Anime<span class="text-primary">Shop</span>
                    </span>
                    <p class="mt-2 text-sm text-neutral-muted leading-relaxed">
                        Thiên đường đồ anime — figure, áo, manga, sticker chính hãng.
                    </p>
                </div>

                {{-- Navigation --}}
                <div>
                    <h3 class="text-sm font-semibold text-neutral-text uppercase tracking-wide mb-3">
                        Danh mục
                    </h3>
                    <ul class="space-y-2 text-sm text-neutral-muted">
                        <li><a href="{{ route('products.index') }}?category=figure" class="hover:text-primary transition-colors">Figure</a></li>
                        <li><a href="{{ route('products.index') }}?category=ao" class="hover:text-primary transition-colors">Áo</a></li>
                        <li><a href="{{ route('products.index') }}?category=manga" class="hover:text-primary transition-colors">Manga</a></li>
                        <li><a href="{{ route('products.index') }}?category=sticker" class="hover:text-primary transition-colors">Sticker</a></li>
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
                            <a href="mailto:hello@animeshop.vn" class="hover:text-primary transition-colors">
                                hello@animeshop.vn
                            </a>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                            </svg>
                            <span>0900 000 000</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 py-5 text-center text-xs text-neutral-muted">
                © {{ date('Y') }} AnimeShop. All rights reserved.
            </div>
        </x-container>
    </footer>

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

</body>
</html>
