<x-app-layout title="404 — Không tìm thấy trang">

    <x-container class="py-12 lg:py-20">
        <div class="flex flex-col lg:flex-row items-center justify-center gap-8 lg:gap-16">

            {{-- Ảnh --}}
            <div class="w-full max-w-sm lg:max-w-md flex-shrink-0">
                <img src="/images/404.png"
                     alt="Trang không tìm thấy"
                     class="w-full h-auto drop-shadow-xl">
            </div>

            {{-- Text --}}
            <div class="flex flex-col items-center lg:items-start text-center lg:text-left gap-4">
                <div class="text-8xl font-black text-primary-light select-none leading-none">
                    404
                </div>
                <h1 class="text-2xl lg:text-3xl font-bold text-neutral-text">
                    Lạc đường rồi...
                </h1>
                <p class="text-sm md:text-base text-neutral-muted max-w-xs">
                    Trang này không tồn tại hoặc đã bị xóa. Cùng cô ấy tìm đường về nhé!
                </p>
                <div class="flex flex-wrap justify-center lg:justify-start gap-3 mt-2">
                    <x-button href="{{ route('home') }}" variant="primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 12 11.204 3.045c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                        Về trang chủ
                    </x-button>
                    <x-button href="{{ route('products.index') }}" variant="secondary">
                        Xem sản phẩm
                    </x-button>
                </div>
            </div>

        </div>
    </x-container>

</x-app-layout>
