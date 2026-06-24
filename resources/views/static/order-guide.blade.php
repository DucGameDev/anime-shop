<x-app-layout
    title="Hướng dẫn đặt hàng — AnimeShop"
    description="Hướng dẫn 6 bước đặt hàng tại AnimeShop: tìm sản phẩm, thêm giỏ hàng, thanh toán chuyển khoản. Đơn giản, nhanh chóng, an toàn."
>

    {{-- Hero strip --}}
    <div class="bg-gradient-to-r from-primary-dark via-primary to-secondary py-10 lg:py-14">
        <x-container>
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-white">Hướng dẫn đặt hàng</h1>
                    <p class="mt-1 text-sm text-white/75">Đặt hàng tại AnimeShop chỉ mất vài phút — theo 6 bước đơn giản.</p>
                </div>
            </div>
        </x-container>
    </div>

    <x-container class="py-10 lg:py-14">
        <div class="mx-auto max-w-3xl">

            {{-- Breadcrumb --}}
            <nav class="mb-8 flex items-center gap-1.5 text-sm text-neutral-muted">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Trang chủ</a>
                <span>/</span>
                <span class="text-neutral-text">Hướng dẫn đặt hàng</span>
            </nav>

            {{-- Các bước --}}
            <div class="space-y-6">
                @foreach ([
                    ['num' => '1', 'title' => 'Tìm sản phẩm yêu thích', 'desc' => 'Duyệt qua trang Sản phẩm hoặc tìm theo danh mục (Figure, Áo, Manga, Sticker). Nhấn vào sản phẩm để xem chi tiết ảnh, giá và mô tả đầy đủ.'],
                    ['num' => '2', 'title' => 'Thêm vào giỏ hàng', 'desc' => 'Chọn số lượng mong muốn (tối đa theo tồn kho thực tế), sau đó nhấn "Thêm vào giỏ". Icon giỏ hàng trên đầu trang sẽ cập nhật số lượng ngay lập tức.'],
                    ['num' => '3', 'title' => 'Kiểm tra giỏ hàng', 'desc' => 'Nhấn vào icon giỏ hàng để xem lại danh sách sản phẩm. Bạn có thể tăng/giảm số lượng hoặc xóa sản phẩm không cần thiết trước khi thanh toán.'],
                    ['num' => '4', 'title' => 'Điền thông tin giao hàng', 'desc' => 'Nhấn "Tiến hành thanh toán", điền đầy đủ họ tên, số điện thoại và địa chỉ giao hàng. Kiểm tra kỹ địa chỉ để tránh giao nhầm.'],
                    ['num' => '5', 'title' => 'Thanh toán chuyển khoản', 'desc' => 'Sau khi đặt hàng thành công, bạn sẽ nhận được mã QR chuyển khoản. Quét mã bằng app ngân hàng, nội dung chuyển khoản sẽ tự điền sẵn mã đơn hàng. Đơn hàng được xác nhận sau khi shop nhận được thanh toán.'],
                    ['num' => '6', 'title' => 'Nhận hàng & đánh giá', 'desc' => 'Shop sẽ cập nhật trạng thái đơn hàng từ "Chờ xử lý" → "Đang giao" → "Hoàn thành". Sau khi nhận hàng, bạn có thể để lại đánh giá sản phẩm ngay trong trang đơn hàng hoặc trang chi tiết sản phẩm.'],
                ] as $step)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white font-bold text-sm">
                            {{ $step['num'] }}
                        </div>
                        <div class="pt-1.5">
                            <h2 class="font-semibold text-neutral-text text-base">{{ $step['title'] }}</h2>
                            <p class="mt-1 text-sm text-neutral-muted leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Lưu ý --}}
            <div class="mt-10 rounded-xl bg-primary-light border border-primary/20 p-5">
                <p class="text-sm font-semibold text-primary-dark mb-2">Lưu ý</p>
                <ul class="space-y-1.5 text-sm text-neutral-text list-disc list-inside">
                    <li>Đơn hàng chưa thanh toán sẽ được giữ trong 24 giờ, sau đó tự động huỷ.</li>
                    <li>Vui lòng kiểm tra kỹ địa chỉ và số điện thoại trước khi đặt hàng.</li>
                    <li>Mọi thắc mắc liên hệ email <a href="mailto:ducdev.work@gmail.com" class="text-primary hover:underline">ducdev.work@gmail.com</a>.</li>
                </ul>
            </div>

            {{-- Link sang trang khác --}}
            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('static.payment') }}" class="text-sm text-primary hover:underline">Hình thức thanh toán →</a>
                <a href="{{ route('static.shipping') }}" class="text-sm text-primary hover:underline">Chính sách vận chuyển →</a>
                <a href="{{ route('static.returns') }}" class="text-sm text-primary hover:underline">Chính sách đổi trả →</a>
            </div>

        </div>
    </x-container>
</x-app-layout>
