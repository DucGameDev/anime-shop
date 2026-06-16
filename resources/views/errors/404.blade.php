<x-app-layout title="404 — Không tìm thấy trang">

    <x-container class="py-16 lg:py-24">
        <div class="flex flex-col items-center text-center gap-5">

            {{-- Illustration --}}
            <div class="relative">
                <div class="text-8xl lg:text-9xl font-black text-primary-light select-none">
                    404
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="h-16 w-16 lg:h-20 lg:w-20 text-primary opacity-80"
                         xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </div>
            </div>

            {{-- Text --}}
            <div class="mt-2">
                <h1 class="text-2xl lg:text-3xl font-bold text-neutral-text">
                    Không tìm thấy trang
                </h1>
                <p class="mt-2 text-sm md:text-base text-neutral-muted max-w-sm">
                    Trang bạn đang tìm không tồn tại hoặc đã bị xóa. Hãy quay về trang chủ nhé!
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap justify-center gap-3 mt-2">
                <x-button variant="primary">
                    <a href="{{ route('home') }}" class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 12 11.204 3.045c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                        Về trang chủ
                    </a>
                </x-button>
                <x-button variant="secondary">
                    <a href="{{ route('products.index') }}">
                        Xem sản phẩm
                    </a>
                </x-button>
            </div>

        </div>
    </x-container>

</x-app-layout>
