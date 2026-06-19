<div x-data="chatWidget()" class="fixed bottom-6 right-6 z-[60]">

    {{-- ===== CỬA SỔ CHAT ===== --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-3"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-3"
        class="absolute bottom-[4.5rem] right-0 w-[min(340px,calc(100vw-3rem))] rounded-2xl bg-white shadow-2xl border border-gray-100 flex flex-col overflow-hidden"
        style="height:500px;display:none"
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary-dark to-primary px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="relative flex-shrink-0">
                    <img src="/images/bot-avatar.png" alt="Bot" class="h-9 w-9 rounded-full object-cover object-top ring-2 ring-white/30">
                    <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-green-400 border-2 border-white"></span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white leading-none">AnimeShop</p>
                    <p class="text-xs text-white/70 mt-0.5">Trợ lý hỗ trợ · Online</p>
                </div>
            </div>
            <button @click="open = false" class="text-white/60 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Vùng tin nhắn --}}
        <div x-ref="messages" class="flex-1 overflow-y-auto px-3 py-4 space-y-3 bg-gray-50/60">

            <template x-for="(msg, i) in messages" :key="i">
                <div>
                    {{-- Bot --}}
                    <template x-if="msg.from === 'bot'">
                        <div class="flex items-end gap-2">
                            <img src="/images/bot-avatar.png" alt="Bot" class="flex-shrink-0 h-6 w-6 rounded-full object-cover object-top mb-0.5">
                            <div class="max-w-[78%]">
                                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-3.5 py-2.5 text-sm text-neutral-text shadow-sm leading-relaxed">
                                    <span x-text="msg.text"></span>
                                    {{-- Cursor nhấp nháy khi đang gõ --}}
                                    <span
                                        x-show="!msg.done"
                                        class="inline-block w-0.5 h-3.5 bg-neutral-muted/70 ml-0.5 align-middle"
                                        style="animation: blink 0.9s step-end infinite"
                                    ></span>
                                </div>
                                {{-- Link chỉ hiện sau khi gõ xong --}}
                                <template x-if="msg.link && msg.done">
                                    <a :href="msg.link.url"
                                       class="mt-1.5 inline-flex items-center gap-1 text-xs text-primary font-medium hover:underline">
                                        <span x-text="msg.link.text"></span>
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- User --}}
                    <template x-if="msg.from === 'user'">
                        <div class="flex justify-end">
                            <div class="max-w-[78%] bg-primary rounded-2xl rounded-br-sm px-3.5 py-2.5 text-sm text-white leading-relaxed"
                                 x-text="msg.text"></div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Typing indicator (3 chấm) --}}
            <div x-show="typing" class="flex items-end gap-2">
                <img src="/images/bot-avatar.png" alt="Bot" class="flex-shrink-0 h-6 w-6 rounded-full object-cover object-top">
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1 items-center">
                        <span class="h-2 w-2 rounded-full bg-neutral-muted/60 animate-bounce" style="animation-delay:0ms"></span>
                        <span class="h-2 w-2 rounded-full bg-neutral-muted/60 animate-bounce" style="animation-delay:160ms"></span>
                        <span class="h-2 w-2 rounded-full bg-neutral-muted/60 animate-bounce" style="animation-delay:320ms"></span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Input --}}
        <div class="border-t border-gray-100 bg-white flex-shrink-0 px-3 py-2.5">
            <form @submit.prevent="send()" class="flex items-center gap-2">
                <input
                    x-model="inputText"
                    x-ref="input"
                    type="text"
                    placeholder="Nhập câu hỏi của bạn..."
                    :disabled="busy"
                    autocomplete="off"
                    class="flex-1 text-sm rounded-full border border-gray-200 bg-gray-50 px-4 py-2 focus:outline-none focus:border-primary focus:bg-white transition-colors disabled:opacity-50"
                >
                <button
                    type="submit"
                    :disabled="!inputText.trim() || busy"
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-primary text-white transition-all hover:bg-primary-dark disabled:opacity-40 disabled:cursor-not-allowed"
                    aria-label="Gửi"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                    </svg>
                </button>
            </form>
        </div>

    </div>

    {{-- ===== NÚT MỞ CHAT ===== --}}
    <button
        @click="toggle()"
        class="relative flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-primary-dark to-secondary text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200"
        aria-label="Chat hỗ trợ"
    >
        <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
        </svg>
        <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:none">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
        </svg>
        <span x-show="!hasOpened" class="absolute -top-0.5 -right-0.5 h-3.5 w-3.5 rounded-full bg-secondary border-2 border-white animate-pulse"></span>
    </button>

</div>

<style>
@keyframes blink { 0%, 100% { opacity: 1 } 50% { opacity: 0 } }
</style>

<script>
function chatWidget() {
    return {
        open: false,
        typing: false,
        busy: false,
        hasOpened: false,
        inputText: '',
        messages: [],

        scenarios: {
            order: {
                keywords: /đặt hàng|mua hàng|cách mua|đặt đơn|order|mua như thế nào|mua sao|làm sao mua/,
                reply: 'Chọn sản phẩm → thêm vào giỏ → điền địa chỉ giao hàng → quét QR thanh toán. Đơn được xác nhận trong 30 phút sau khi chuyển khoản! 🎉',
                link: { text: 'Xem hướng dẫn chi tiết', url: '/huong-dan-dat-hang' },
            },
            shipping: {
                keywords: /ship|vận chuyển|phí giao|giao hàng|freeship|miễn phí ship|phí ship|bao lâu|mấy ngày|thời gian giao/,
                reply: 'Phí ship 25.000₫ – 55.000₫ tuỳ khu vực. Miễn phí ship đơn từ 300.000₫ 🎁 Thời gian giao 1–5 ngày tùy địa chỉ.',
                link: { text: 'Xem bảng phí vận chuyển', url: '/chinh-sach-van-chuyen' },
            },
            payment: {
                keywords: /thanh toán|trả tiền|chuyển khoản|qr|cod|momo|zalopay|trả bằng gì|thanh toán như thế nào|trả như thế nào|payment/,
                reply: 'Hỗ trợ QR chuyển khoản (tất cả ngân hàng, MoMo, ZaloPay) và COD nội thành HCM & Hà Nội. Không phụ phí khi thanh toán QR! 💳',
                link: { text: 'Xem hình thức thanh toán', url: '/hinh-thuc-thanh-toan' },
            },
            returns: {
                keywords: /đổi|trả hàng|hoàn tiền|đổi trả|return|hoàn|lỗi|hỏng|sai hàng|trả lại/,
                reply: 'Đổi trả trong 7 ngày nếu lỗi từ shop. Shop xử lý trong 24h và hoàn tiền 100% nếu hàng lỗi. Cam kết chính hãng 100%! ✅',
                link: { text: 'Xem chính sách đổi trả', url: '/chinh-sach-doi-tra' },
            },
            track: {
                keywords: /theo dõi|đơn hàng|trạng thái đơn|đơn của tôi|kiểm tra đơn|track|đơn đâu|hàng đâu/,
                reply: 'Vào mục Đơn hàng của tôi để theo dõi trạng thái. Đơn cập nhật theo thứ tự: Chờ xử lý → Đang giao → Hoàn thành 📦',
                link: { text: 'Đến trang đơn hàng của tôi', url: '/account/orders' },
            },
            contact: {
                keywords: /liên hệ|contact|email|điện thoại|gọi|nhắn tin|hỏi thêm|tư vấn|hỗ trợ/,
                reply: 'Liên hệ qua email ducdev.work@gmail.com. Phản hồi trong giờ 8:00–22:00 hàng ngày, kể cả cuối tuần! 😊',
                link: null,
            },
            authentic: {
                keywords: /chính hãng|hàng thật|fake|giả|nhái|authentic|nguồn gốc|xuất xứ|tem|seal/,
                reply: '100% hàng chính hãng có tem nhập khẩu rõ ràng. Nếu phát hiện hàng giả, AnimeShop hoàn tiền gấp đôi — không cần hoàn hàng! 🔒',
                link: { text: 'Xem chính sách đổi trả', url: '/chinh-sach-doi-tra' },
            },
            stock: {
                keywords: /còn hàng|hết hàng|tồn kho|còn không|stock|sắp hết|out of stock|bao giờ có/,
                reply: 'Tồn kho hiển thị trực tiếp trên trang sản phẩm. Nếu sản phẩm hết hàng, bạn có thể liên hệ shop để đặt trước — mình sẽ thông báo khi có hàng mới! 📬',
                link: { text: 'Xem tất cả sản phẩm', url: '/products' },
            },
            cancel: {
                keywords: /huỷ|hủy đơn|cancel|không mua nữa|đổi ý|muốn huỷ/,
                reply: 'Bạn có thể huỷ đơn hàng khi đơn còn trạng thái "Chờ xử lý" và chưa chuyển khoản. Liên hệ email để mình hỗ trợ nhanh nhất nhé! ⚡',
                link: null,
            },
            preorder: {
                keywords: /preorder|đặt trước|order trước|pre-order|chưa có hàng|sắp ra|coming soon/,
                reply: 'AnimeShop nhận đặt trước một số dòng figure và manga mới. Liên hệ email kèm tên sản phẩm để mình báo giá và thời gian về hàng nhé! 🗓️',
                link: null,
            },
            size: {
                keywords: /size|cỡ|áo|kích thước|s m l xl|bảng size|vừa không|đo|cm/,
                reply: 'Áo anime tại shop có các size S, M, L, XL, XXL theo chuẩn Việt Nam. Mỗi sản phẩm áo đều có bảng size chi tiết trong phần mô tả — bạn check thêm nhé! 👕',
                link: { text: 'Xem sản phẩm áo', url: '/products?category=ao' },
            },
            discount: {
                keywords: /giảm giá|voucher|mã giảm|khuyến mãi|sale|coupon|ưu đãi|discount|promo/,
                reply: 'Shop thường xuyên có chương trình ưu đãi qua email và fanpage. Follow page để không bỏ lỡ nhé! Hiện tại có miễn phí ship đơn từ 300.000₫ 🎁',
                link: { text: 'Xem sản phẩm đang có', url: '/products' },
            },
            gift: {
                keywords: /quà|tặng|gift|gói quà|wrap|sinh nhật|birthday|present/,
                reply: 'Shop có thể gói quà tặng kèm thiệp viết tay miễn phí theo yêu cầu! Ghi chú nội dung thiệp vào phần địa chỉ khi đặt hàng, mình sẽ chuẩn bị chu đáo 🎁🎀',
                link: null,
            },
            bulk: {
                keywords: /mua sỉ|số lượng lớn|bulk|wholesale|cộng tác|đại lý|reseller|nhiều cái/,
                reply: 'Shop hỗ trợ mua sỉ cho đơn từ 10 sản phẩm trở lên với chiết khấu hấp dẫn. Liên hệ email để được tư vấn bảng giá sỉ chi tiết nhé! 📦',
                link: null,
            },
            hours: {
                keywords: /giờ làm việc|mấy giờ|bao giờ|lúc nào|giờ mở cửa|hoạt động|giờ hành chính/,
                reply: 'AnimeShop hoạt động online 24/7 — bạn đặt hàng lúc nào cũng được! 🕐 Đội hỗ trợ trực tuyến và xử lý đơn từ 8:00–22:00 hàng ngày, kể cả cuối tuần.',
                link: null,
            },
            figure: {
                keywords: /figure|mô hình|figurine|statue|nendoroid|funko|prize figure|scale|garage kit/,
                reply: 'Shop có đa dạng figure từ nhiều series: Naruto, One Piece, Demon Slayer, Genshin Impact... Cả Nendoroid, prize figure đến scale 1/7! Tất cả đều chính hãng có seal. 🗿',
                link: { text: 'Xem danh mục Figure', url: '/products?category=figure' },
            },
            manga: {
                keywords: /manga|truyện|light novel|comic|volume|tập|bộ|đọc/,
                reply: 'Shop có manga bản tiếng Việt (NXB Kim Đồng, Trẻ) và bản tiếng Nhật. Nhiều bộ hot như One Piece, Jujutsu Kaisen, Attack on Titan... Liên hệ để hỏi bộ cụ thể nhé! 📚',
                link: { text: 'Xem danh mục Manga', url: '/products?category=manga' },
            },
            greeting: {
                keywords: /^(xin chào|chào|hello|hi|hey|alo|good morning|good afternoon|ờ|ừ|okay|ok)$/,
                reply: 'Chào bạn! 😊 Mình là trợ lý AnimeShop. Bạn đang cần hỗ trợ về vấn đề gì? Đặt hàng, vận chuyển, thanh toán, hay tìm sản phẩm cụ thể?',
                link: null,
            },
        },

        toggle() {
            this.open = !this.open;
            if (this.open && this.messages.length === 0) {
                this.hasOpened = true;
                this.init();
            } else if (this.open) {
                this.$nextTick(() => this.$refs.input?.focus());
            }
        },

        init() {
            setTimeout(() => {
                this.typeMessage(
                    'Xin chào! 👋 Mình là trợ lý AnimeShop. Bạn có thể hỏi mình về đặt hàng, phí ship, thanh toán, đổi trả hoặc theo dõi đơn hàng nhé!',
                    null
                );
            }, 400);
        },

        send() {
            const text = this.inputText.trim();
            if (!text || this.busy) return;

            this.inputText = '';
            this.messages.push({ from: 'user', text, done: true });
            this.scrollToBottom();
            this.respond(text);
        },

        async respond(text) {
            const t = text.toLowerCase();
            const match = Object.values(this.scenarios).find(s => s.keywords.test(t));

            const reply = match
                ? match.reply
                : 'Mình chưa hiểu câu hỏi lắm 😅 Thử hỏi về: đặt hàng, phí ship, thanh toán, đổi trả hoặc theo dõi đơn hàng nhé!';

            await this.typeMessage(reply, match?.link ?? null);
            this.$nextTick(() => this.$refs.input?.focus());
        },

        async typeMessage(text, link) {
            this.busy = true;

            // Typing indicator (3 chấm) — 1.2–1.8s tuỳ độ dài text
            this.typing = true;
            this.scrollToBottom();
            const thinkTime = Math.min(1800, 1200 + text.length * 4);
            await new Promise(r => setTimeout(r, thinkTime));
            this.typing = false;

            // Thêm tin nhắn rỗng, bắt đầu typewriter
            const msg = { from: 'bot', text: '', link: null, done: false };
            this.messages.push(msg);
            const idx = this.messages.length - 1;
            this.scrollToBottom();

            // Tốc độ gõ: 20–40ms/ký tự, tổng ~2–3s
            const charDelay = Math.max(20, Math.min(40, 2800 / text.length));
            for (let i = 0; i < text.length; i++) {
                await new Promise(r => setTimeout(r, charDelay));
                this.messages[idx].text += text[i];
                // Scroll mỗi 8 ký tự để không nặng
                if (i % 8 === 0) this.scrollToBottom();
            }

            this.messages[idx].done = true;
            this.messages[idx].link = link || null;
            this.scrollToBottom();
            this.busy = false;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messages;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },
    };
}
</script>
