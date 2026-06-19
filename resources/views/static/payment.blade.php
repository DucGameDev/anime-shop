<x-app-layout title="Hình thức thanh toán — AnimeShop">

    {{-- Hero strip --}}
    <div class="bg-gradient-to-r from-primary-dark via-primary to-secondary py-10 lg:py-14">
        <x-container>
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-white">Hình thức thanh toán</h1>
                    <p class="mt-1 text-sm text-white/75">An toàn, nhanh chóng, không phụ phí ẩn.</p>
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
                <span class="text-neutral-text">Hình thức thanh toán</span>
            </nav>

            <div class="space-y-6">

                {{-- Chuyển khoản ngân hàng --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-light">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-neutral-text">Chuyển khoản ngân hàng (QR)</h2>
                            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Phổ biến nhất</span>
                        </div>
                    </div>
                    <ul class="space-y-2 text-sm text-neutral-muted list-disc list-inside">
                        <li>Sau khi đặt hàng thành công, bạn nhận được mã QR VietQR.</li>
                        <li>Quét mã bằng app ngân hàng bất kỳ (Vietcombank, MB, Techcombank, MoMo, ZaloPay...).</li>
                        <li>Nội dung chuyển khoản tự điền sẵn mã đơn hàng, không cần nhập tay.</li>
                        <li>Đơn hàng được xác nhận trong vòng <strong class="text-neutral-text">30 phút</strong> trong giờ hành chính.</li>
                        <li>Không áp dụng phụ phí khi chuyển khoản.</li>
                    </ul>
                </div>

                {{-- COD --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning-light">
                            <svg class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-neutral-text">Thanh toán khi nhận hàng (COD)</h2>
                            <span class="text-xs font-medium text-warning bg-warning-light px-2 py-0.5 rounded-full">Chỉ nội thành HCM & Hà Nội</span>
                        </div>
                    </div>
                    <ul class="space-y-2 text-sm text-neutral-muted list-disc list-inside">
                        <li>Trả tiền mặt trực tiếp cho shipper khi nhận hàng.</li>
                        <li>Áp dụng cho đơn hàng tại TP.HCM và Hà Nội, tối đa <strong class="text-neutral-text">2.000.000₫</strong>.</li>
                        <li>Phụ phí COD: <strong class="text-neutral-text">15.000₫</strong>/đơn.</li>
                        <li>Vui lòng chuẩn bị tiền lẻ, shipper không đổi tiền lớn.</li>
                    </ul>
                </div>

            </div>

            {{-- Bảo mật --}}
            <div class="mt-8 rounded-xl bg-primary-light border border-primary/20 p-5">
                <p class="text-sm font-semibold text-primary-dark mb-1">Bảo mật thanh toán</p>
                <p class="text-sm text-neutral-muted">
                    AnimeShop không lưu trữ thông tin thẻ ngân hàng hay tài khoản thanh toán của bạn.
                    Mọi giao dịch chuyển khoản được xử lý trực tiếp qua hệ thống ngân hàng an toàn.
                </p>
            </div>

            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('static.order-guide') }}" class="text-sm text-primary hover:underline">← Hướng dẫn đặt hàng</a>
                <a href="{{ route('static.shipping') }}" class="text-sm text-primary hover:underline">Chính sách vận chuyển →</a>
                <a href="{{ route('static.returns') }}" class="text-sm text-primary hover:underline">Chính sách đổi trả →</a>
            </div>

        </div>
    </x-container>
</x-app-layout>
