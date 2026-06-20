<x-app-layout title="Lỗi máy chủ — AnimeShop">
    <x-container class="py-16">
        <div class="flex flex-col items-center justify-center text-center gap-6">

            <img src="/images/errors/500.png"
                 alt="500 Server Error"
                 class="w-56 sm:w-72 drop-shadow-md select-none"
                 loading="eager">

            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-neutral-text">Ồ không, máy chủ gặp sự cố!</h1>
                <p class="text-neutral-muted max-w-md">
                    Server đang có chút trục trặc. Đội ngũ kỹ thuật đã được thông báo — bạn thử lại sau nhé!
                </p>
            </div>

            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-primary-dark transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Về trang chủ
                </a>
                <button onclick="location.reload()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 text-neutral-text font-medium hover:border-primary hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Thử lại
                </button>
            </div>

        </div>
    </x-container>
</x-app-layout>
