<x-app-layout title="Chính sách đổi trả — AnimeShop">

    {{-- Hero strip --}}
    <div class="bg-gradient-to-r from-primary-dark via-primary to-secondary py-10 lg:py-14">
        <x-container>
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-white">Chính sách đổi trả</h1>
                    <p class="mt-1 text-sm text-white/75">Đổi trả trong 7 ngày — hoàn tiền 100% nếu lỗi từ shop.</p>
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
                <span class="text-neutral-text">Chính sách đổi trả</span>
            </nav>

            {{-- Điều kiện --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 mb-6">
                <h2 class="font-semibold text-neutral-text mb-4">Điều kiện đổi trả</h2>
                <div class="space-y-3">
                    @foreach ([
                        ['ok' => true,  'text' => 'Sản phẩm bị lỗi do nhà sản xuất (sơn bong, gãy phụ kiện, in sai, thiếu linh kiện).'],
                        ['ok' => true,  'text' => 'Giao sai sản phẩm (sai tên, sai màu, sai kích thước) so với đơn đặt hàng.'],
                        ['ok' => true,  'text' => 'Sản phẩm bị hỏng, móp méo do quá trình vận chuyển.'],
                        ['ok' => true,  'text' => 'Yêu cầu đổi trả trong vòng <strong>7 ngày</strong> kể từ ngày nhận hàng.'],
                        ['ok' => false, 'text' => 'Sản phẩm đã được mở seal, tháo lắp hoặc sử dụng (trừ trường hợp phát hiện lỗi khi mở hộp).'],
                        ['ok' => false, 'text' => 'Sản phẩm bị hư hỏng do người dùng (rơi vỡ, tiếp xúc nước, tự sửa chữa).'],
                        ['ok' => false, 'text' => 'Đổi trả vì lý do cá nhân (không thích, đặt nhầm) sau khi đã xác nhận đơn hàng.'],
                    ] as $item)
                        <div class="flex items-start gap-3 text-sm">
                            @if ($item['ok'])
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                                <span class="text-neutral-text">{!! $item['text'] !!}</span>
                            @else
                                <svg class="h-4 w-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                                <span class="text-neutral-muted">{!! $item['text'] !!}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Quy trình --}}
            <div class="rounded-xl border border-gray-200 bg-wh
            ite p-6 mb-6">
                <h2 class="font-semibold text-neutral-text mb-4">Quy trình đổi trả</h2>
                <div class="space-y-4">
                    @foreach ([
                        ['num' => '1', 'title' => 'Liên hệ AnimeShop', 'desc' => 'Gửi email đến ducdev.work@gmail.com với tiêu đề "Yêu cầu đổi trả #XXXXXX", đính kèm ảnh/video sản phẩm lỗi và mã đơn hàng.'],
                        ['num' => '2', 'title' => 'Xác nhận từ shop', 'desc' => 'Đội ngũ AnimeShop phản hồi trong vòng 24 giờ làm việc. Nếu yêu cầu hợp lệ, bạn nhận được hướng dẫn gửi hàng về.'],
                        ['num' => '3', 'title' => 'Gửi hàng về', 'desc' => 'Đóng gói kỹ và gửi về địa chỉ shop. Phí gửi hàng về do AnimeShop chịu nếu lỗi xuất phát từ phía chúng tôi.'],
                        ['num' => '4', 'title' => 'Nhận hàng đổi / hoàn tiền', 'desc' => 'Sau khi nhận và kiểm tra hàng (1–2 ngày làm việc), AnimeShop gửi hàng mới hoặc hoàn tiền 100% trong vòng 3–5 ngày làm việc.'],
                    ] as $step)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 flex h-7 w-7 items-center justify-center rounded-full bg-primary text-white font-bold text-xs">
                                {{ $step['num'] }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-neutral-text">{{ $step['title'] }}</p>
                                <p class="text-sm text-neutral-muted mt-0.5 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cam kết --}}
            <div class="rounded-xl bg-primary-light border border-primary/20 p-5">
                <p class="text-sm font-semibold text-primary-dark mb-1">Cam kết của AnimeShop</p>
                <p class="text-sm text-neutral-muted">
                    100% hàng chính hãng có tem nhập khẩu. Nếu phát hiện hàng giả, hàng nhái, AnimeShop
                    hoàn trả <strong class="text-neutral-text">100%</strong> giá trị đơn hàng, không cần hoàn hàng.
                </p>
            </div>

            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('static.order-guide') }}" class="text-sm text-primary hover:underline">← Hướng dẫn đặt hàng</a>
                <a href="{{ route('static.payment') }}" class="text-sm text-primary hover:underline">← Hình thức thanh toán</a>
                <a href="{{ route('static.shipping') }}" class="text-sm text-primary hover:underline">← Chính sách vận chuyển</a>
            </div>

        </div>
    </x-container>
</x-app-layout>
