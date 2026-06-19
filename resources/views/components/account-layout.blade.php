@props(['title'])

<x-app-layout :title="$title . ' — AnimeShop'">
    <x-container class="py-8 lg:py-12">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Sidebar navigation --}}
            <div class="lg:w-56 shrink-0">

                {{-- Mobile: horizontal scrollable tabs --}}
                <div class="flex overflow-x-auto gap-1 rounded-xl bg-white p-1.5 shadow-sm lg:hidden">

                    <a href="{{ route('account.orders') }}"
                       class="flex shrink-0 items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap
                           {{ request()->routeIs('account.orders')
                               ? 'text-primary font-semibold bg-primary-light'
                               : 'text-neutral-text hover:bg-gray-50' }}">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                        </svg>
                        Đơn hàng
                    </a>

                    <a href="{{ route('account.profile') }}"
                       class="flex shrink-0 items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap
                           {{ request()->routeIs('account.profile')
                               ? 'text-primary font-semibold bg-primary-light'
                               : 'text-neutral-text hover:bg-gray-50' }}">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                        Cá nhân
                    </a>

                    <a href="{{ route('account.addresses') }}"
                       class="flex shrink-0 items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap
                           {{ request()->routeIs('account.addresses')
                               ? 'text-primary font-semibold bg-primary-light'
                               : 'text-neutral-text hover:bg-gray-50' }}">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        Địa chỉ
                    </a>

                    <a href="{{ route('account.favorites') }}"
                       class="flex shrink-0 items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap
                           {{ request()->routeIs('account.favorites')
                               ? 'text-primary font-semibold bg-primary-light'
                               : 'text-neutral-text hover:bg-gray-50' }}">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                        </svg>
                        Yêu thích
                    </a>

                </div>

                {{-- Desktop: vertical nav --}}
                <div class="hidden lg:block rounded-xl bg-white shadow-sm overflow-hidden">
                    <div class="p-3 space-y-0.5">

                        <a href="{{ route('account.orders') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm transition-colors
                               {{ request()->routeIs('account.orders')
                                   ? 'text-primary font-semibold bg-primary-light'
                                   : 'text-neutral-text hover:bg-gray-50' }}">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                            </svg>
                            Đơn hàng của tôi
                        </a>

                        <a href="{{ route('account.profile') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm transition-colors
                               {{ request()->routeIs('account.profile')
                                   ? 'text-primary font-semibold bg-primary-light'
                                   : 'text-neutral-text hover:bg-gray-50' }}">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                            Thông tin cá nhân
                        </a>

                        <a href="{{ route('account.addresses') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm transition-colors
                               {{ request()->routeIs('account.addresses')
                                   ? 'text-primary font-semibold bg-primary-light'
                                   : 'text-neutral-text hover:bg-gray-50' }}">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                            Sổ địa chỉ
                        </a>

                        <a href="{{ route('account.favorites') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm transition-colors
                               {{ request()->routeIs('account.favorites')
                                   ? 'text-primary font-semibold bg-primary-light'
                                   : 'text-neutral-text hover:bg-gray-50' }}">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                            </svg>
                            Yêu thích
                        </a>

                    </div>
                </div>

            </div>

            {{-- Main content --}}
            <div class="flex-1 min-w-0">
                {{ $slot }}
            </div>

        </div>
    </x-container>
</x-app-layout>
