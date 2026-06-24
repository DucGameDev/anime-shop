<x-app-layout
    title="Chính sách vận chuyển — AnimeShop"
    description="Giao hàng toàn quốc qua GHTK và J&T Express. Miễn phí ship đơn từ 300.000₫. Nội thành 1–2 ngày, tỉnh thành 2–5 ngày. Đóng gói cẩn thận, bồi thường 100% nếu thất lạc."
>

    {{-- Hero strip --}}
    <div class="bg-gradient-to-r from-primary-dark via-primary to-secondary py-10 lg:py-14">
        <x-container>
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-white">Chính sách vận chuyển</h1>
                    <p class="mt-1 text-sm text-white/75">Giao hàng toàn quốc — miễn phí ship đơn từ 300.000₫.</p>
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
                <span class="text-neutral-text">Chính sách vận chuyển</span>
            </nav>

            {{-- Đối tác --}}
            <p class="text-sm text-neutral-muted mb-6">AnimeShop giao hàng toàn quốc qua đối tác vận chuyển GHTK và J&T Express.</p>

            {{-- Bảng phí ship --}}
            <div class="rounded-xl border border-gray-200 bg-white overflow-hidden mb-6">
                <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="font-semibold text-neutral-text">Bảng phí vận chuyển</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="grid grid-cols-3 px-5 py-3 text-xs font-semibold text-neutral-muted uppercase tracking-wide">
                        <span>Khu vực</span>
                        <span class="text-center">Thời gian</span>
                        <span class="text-right">Phí ship</span>
                    </div>
                    @foreach ([
                        ['area' => 'Nội thành TP.HCM & HN', 'time' => '1 – 2 ngày', 'fee' => '25.000₫'],
                        ['area' => 'Tỉnh thành lân cận',    'time' => '2 – 3 ngày', 'fee' => '35.000₫'],
                        ['area' => 'Các tỉnh còn lại',      'time' => '3 – 5 ngày', 'fee' => '40.000₫'],
                        ['area' => 'Vùng sâu / hải đảo',    'time' => '5 – 7 ngày', 'fee' => '55.000₫'],
                    ] as $row)
                        <div class="grid grid-cols-3 px-5 py-3.5 text-sm">
                            <span class="text-neutral-text font-medium">{{ $row['area'] }}</span>
                            <span class="text-center text-neutral-muted">{{ $row['time'] }}</span>
                            <span class="text-right font-semibold text-neutral-text">{{ $row['fee'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Freeship --}}
            <div class="rounded-xl bg-green-50 border border-green-200 p-5 mb-6 flex items-start gap-3">
                <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">Miễn phí vận chuyển toàn quốc</p>
                    <p class="text-sm text-green-700 mt-0.5">Áp dụng cho đơn hàng từ <strong>300.000₫</strong> trở lên (trừ vùng sâu / hải đảo).</p>
                </div>
            </div>

            {{-- Quy định khác --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <h2 class="font-semibold text-neutral-text">Quy định khác</h2>
                <ul class="space-y-3 text-sm text-neutral-muted list-disc list-inside">
                    <li>Đơn hàng được xử lý và bàn giao cho đơn vị vận chuyển trong vòng <strong class="text-neutral-text">1 ngày làm việc</strong> kể từ khi xác nhận thanh toán.</li>
                    <li>Thời gian giao hàng không tính Chủ nhật và ngày lễ.</li>
                    <li>Sản phẩm được đóng gói cẩn thận bằng túi bọc khí và hộp carton để tránh va đập trong quá trình vận chuyển.</li>
                    <li>Nếu đơn hàng bị thất lạc do lỗi của đơn vị vận chuyển, AnimeShop sẽ hỗ trợ bồi thường 100% giá trị đơn hàng.</li>
                    <li>Khách hàng có thể theo dõi đơn hàng qua trang <a href="{{ route('account.orders') }}" class="text-primary hover:underline">Đơn hàng của tôi</a>.</li>
                </ul>
            </div>

            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('static.order-guide') }}" class="text-sm text-primary hover:underline">← Hướng dẫn đặt hàng</a>
                <a href="{{ route('static.payment') }}" class="text-sm text-primary hover:underline">← Hình thức thanh toán</a>
                <a href="{{ route('static.returns') }}" class="text-sm text-primary hover:underline">Chính sách đổi trả →</a>
            </div>

        </div>
    </x-container>
</x-app-layout>
