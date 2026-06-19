# Master Guide — Xây dựng & Deploy AnimeShop với Claude Code

Đây là tài liệu tổng hợp toàn bộ quy trình: từ cài đặt môi trường, setup project, đến khi đưa website lên production. Dùng kèm 2 file đã tạo trước đó:
- `prompts-anime-shop.md` — chi tiết từng prompt theo thứ tự
- `SKILL.md` + thư mục `agents/` — quy ước và subagent cho Claude Code

---

## 1. Tổng quan dự án

| | |
|---|---|
| Loại site | Bán đồ anime (figure, áo, manga, sticker) |
| Backend | Laravel (PHP 8.2+) |
| Frontend khách | Blade + Livewire + Alpine.js + Tailwind CSS |
| Admin | Filament (`/admin`) |
| Database | MySQL 8 (Docker) |
| Giỏ hàng | Session-based, không cần đăng nhập |
| Storage ảnh | local (dev) → S3-compatible (production) |
| Hạ tầng | Docker (dev + prod), Vercel không dùng (Laravel cần PHP runtime) |

**Màu chủ đạo**: trắng + tím (`primary`) cho thương hiệu/nút chính, hồng (`secondary`) cho nhấn phụ/badge.

---

## 2. Chuẩn bị môi trường

### 2.1. Cài Claude Code

```bash
npm install -g @anthropic-ai/claude-code
```

Yêu cầu Node.js 18+. Kiểm tra: `claude --version`.

### 2.2. Yêu cầu máy

- Docker + Docker Compose đã cài và chạy được
- Git đã cài, đã có tài khoản GitHub
- (Tùy chọn) tài khoản Supabase/AWS/DigitalOcean nếu sẽ dùng S3-compatible storage

### 2.3. Tạo thư mục project và mở Claude Code

```bash
mkdir anime-shop && cd anime-shop
claude
```

---

## 3. Setup file hỗ trợ Claude Code TRƯỚC khi code (quan trọng)

Thứ tự khuyến nghị: tạo các file "context" này **trước** hoặc **ngay sau** Prompt 1, để mọi prompt sau đó đều được Claude Code thực hiện đúng quy ước từ đầu — tránh phải refactor lại nhiều lần.

### 3.1. Cấu trúc file cuối cùng trong project

```
anime-shop/
├── CLAUDE.md                          ← Claude Code đọc đầu tiên mỗi session
├── .claude/
│   ├── skills/
│   │   └── anime-shop-conventions/
│   │       └── SKILL.md               ← quy ước chi tiết (coding/design/CSS)
│   └── agents/
│       ├── laravel-backend.md
│       ├── ui-developer.md
│       ├── filament-admin.md
│       ├── code-reviewer.md
│       └── deploy-ops.md
├── README.md
├── docs/
│   ├── database.md
│   └── routes.md
├── .env.example
├── .env                                ← KHÔNG commit
├── Dockerfile
├── Dockerfile.prod
├── docker-compose.yml
├── docker-compose.prod.yml
├── docker-entrypoint.sh
└── ... (code Laravel: app/, resources/, routes/, database/...)
```

### 3.2. Các bước setup file context

1. Copy `SKILL.md` (đã tạo ở bước trước) vào `.claude/skills/anime-shop-conventions/SKILL.md`
2. Copy 5 file trong `agents/` vào `.claude/agents/`
3. Sau khi chạy Prompt 1 (khởi tạo Laravel), yêu cầu Claude Code:

```
Đọc các file trong .claude/skills/ và .claude/agents/, xác nhận đã hiểu
quy ước project. Sau đó tạo CLAUDE.md ở root tóm tắt lại các quy ước đó
(tổng quan, cấu trúc thư mục, coding standards, design patterns, CSS/
responsive conventions, lệnh thường dùng, trạng thái hiện tại) theo
đúng nội dung trong SKILL.md, không thêm thông tin tự suy diễn.
```

---

## 4. Lộ trình thực hiện (theo `prompts-anime-shop.md`)

Chạy **tuần tự**, mỗi prompt test ngay trước khi sang prompt tiếp theo. Tổng quan các giai đoạn:

### Giai đoạn 1 — Khung project (Prompt 1–3)
- Khởi tạo Laravel + Docker (MySQL, nginx, phpMyAdmin)
- Migration + Seeder cho Product
- Cài Livewire + Alpine, layout chung, color tokens, component dùng chung (`x-button`, `x-input`, `x-container`)

**Kiểm tra**: `localhost` chạy được, `docker compose up -d` ổn định, phpMyAdmin xem được bảng `products`.

### Giai đoạn 2 — Trang khách hàng (Prompt 4–8)
- Trang chủ + ProductCard
- Trang `/products` với filter + search realtime (Livewire)
- Trang chi tiết sản phẩm
- Giỏ hàng (CartService + Livewire Cart/CartIcon)
- Trang `/cart`

**Kiểm tra**: luồng xem sản phẩm → thêm giỏ → xem giỏ hàng hoạt động đầy đủ, responsive đúng ở 375/768/1280px.

### Giai đoạn 3 — Admin & Checkout (Prompt 9, 10, 10b–10d)
- Cài Filament, Resource Product
- Checkout + Order/OrderItem + PlaceOrderAction + trang xác nhận đơn hàng
- Dashboard thống kê
- Resource Category (thay chuỗi cố định)
- Resource Customer (chỉ xem)

**Kiểm tra**: đặt thử 1 đơn hàng từ giỏ → checkout → xem trong `/admin/orders`, stock tự trừ đúng.

### Giai đoạn 4 — Chuẩn bị production (Prompt 11–13)
- Dockerfile.prod (multi-stage)
- Storage ảnh S3-compatible
- `.env.example` đầy đủ, kiểm tra `.gitignore`

### Giai đoạn 5 — Tài liệu & chất lượng (Prompt 14–17)
- Cập nhật `CLAUDE.md` (trạng thái hiện tại)
- `docs/database.md`, `docs/routes.md`
- Cài Pint + Larastan
- Git commit có cấu trúc

---

## 5. Cách làm việc hiệu quả với Claude Code (nhắc lại nguyên tắc)

- **1 prompt = 1 thay đổi nhỏ**, test ngay sau mỗi prompt
- **Copy nguyên lỗi** (terminal/browser console) khi gặp bug, không tự diễn giải lại
- **Commit sau mỗi tính năng** ổn định (`feat:`, `fix:`...)
- Trước khi commit tính năng lớn, chạy thử với `code-reviewer` agent:

```
Dùng code-reviewer agent, review toàn bộ thay đổi từ commit gần nhất
đến hiện tại, đối chiếu với SKILL.md
```

- Khi cần thêm tính năng có UI + logic, có thể giao trực tiếp cho đúng agent:

```
Dùng laravel-backend agent: tạo Action PlaceOrderAction...
Dùng ui-developer agent: tạo trang /checkout theo Action vừa tạo...
```

---

## 6. Checklist trước khi đưa lên production

### Kỹ thuật
- [ ] `APP_ENV=production`, `APP_DEBUG=false` trong `.env` thật trên server
- [ ] `APP_KEY` mới được generate riêng cho production (không dùng key của local)
- [ ] Database production là MySQL thật (không SQLite), đã migrate + seed Category cơ bản
- [ ] Storage ảnh trỏ disk S3-compatible, đã test upload qua Filament
- [ ] `.env`, `.env.production` KHÔNG có trong Git history
- [ ] HEALTHCHECK của container hoạt động
- [ ] SSL (Let's Encrypt/Certbot hoặc Cloudflare) đã cấu hình cho domain

### Chức năng
- [ ] Luồng: xem sản phẩm → thêm giỏ → checkout → xem đơn trong admin → đổi trạng thái → khách xem trạng thái (nếu có) — chạy hết không lỗi
- [ ] Filter/search sản phẩm hoạt động đúng trên cả mobile và desktop
- [ ] Trang 404 hiển thị đúng khi truy cập sản phẩm không tồn tại
- [ ] Admin: tạo/sửa/xóa sản phẩm, đổi category, đổi trạng thái đơn hàng — đều hoạt động

### Vận hành
- [ ] Đã có script/cron backup MySQL định kỳ
- [ ] Đã ghi lại thông tin: domain DNS, nơi lưu `.env` production, tài khoản S3, tài khoản admin Filament (lưu nơi an toàn, không trong chat)

---

## 7. Mở rộng sau khi launch (không gấp)

- Tích hợp cổng thanh toán online (VNPay/MoMo) — dùng `PaymentGateway` interface đã định nghĩa trong quy ước, thêm implementation mới mà không sửa code cũ
- CI/CD (GitHub Actions: test + deploy tự động)
- Cache (Redis cho session/query) khi traffic tăng
- SEO: meta tags từng sản phẩm, sitemap.xml, robots.txt
- Theo dõi lỗi: Sentry hoặc tương đương

---

## 8. Tài liệu liên quan

| File | Nội dung |
|---|---|
| `prompts-anime-shop.md` | 17+ prompt chi tiết, có mô tả giao diện từng phần |
| `SKILL.md` / `anime-shop-conventions.skill` | Quy ước coding/design/CSS đầy đủ cho Claude Code |
| `agents/*.md` | 5 subagent: laravel-backend, ui-developer, filament-admin, code-reviewer, deploy-ops |
| `CLAUDE.md` (tự sinh ở bước 3.2) | Bản tóm tắt quy ước, đọc đầu mỗi session |
| `docs/database.md`, `docs/routes.md` | Tài liệu schema và route (tạo ở Prompt 15) |
