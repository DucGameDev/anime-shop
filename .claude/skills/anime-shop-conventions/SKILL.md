---
name: anime-shop-conventions
description: Quy ước phát triển cho project Laravel "anime-shop" (Livewire + Filament + Docker + MySQL) — bán đồ anime (figure, áo, manga, sticker). LUÔN dùng skill này khi viết, sửa, hoặc review bất kỳ code nào trong project anime-shop, bao gồm Controller, Livewire component, Filament Resource, migration, Blade view, Tailwind/CSS, hoặc khi quyết định nơi đặt business logic (Service/Action/Observer). Áp dụng cả khi user chỉ yêu cầu một thay đổi nhỏ — kiểm tra quy ước trước khi code, không chỉ khi user nhắc tới "convention" hay "standard".
---

# Anime Shop — Quy ước phát triển

Skill này định nghĩa cách viết code, cấu trúc thư mục, và quy ước UI cho project bán đồ anime (Laravel + Livewire + Filament + Docker + MySQL). Đọc kỹ trước khi tạo file mới, sửa code, hoặc đưa ra quyết định kiến trúc.

## 1. Tổng quan project

- Frontend khách hàng: Laravel Blade + Livewire + Alpine.js + Tailwind CSS
- Trang quản trị: Filament (tại `/admin`)
- Database: MySQL (chạy trong Docker), không dùng SQLite cho dự án này
- Giỏ hàng: lưu session, không cần đăng nhập
- Production: Dockerfile.prod multi-stage, ảnh sản phẩm lưu trên S3-compatible storage (S3/Spaces/R2)

## 2. Cấu trúc thư mục — đặt code đúng chỗ

| Loại code | Vị trí | Lý do |
|---|---|---|
| Business logic / nghiệp vụ phức tạp, tái sử dụng | `app/Actions/` (1 class, method `execute()`/`handle()`) | Dễ test, gọi từ Livewire và admin đều được |
| Logic dùng chung nhỏ (giỏ hàng...) | `app/Services/` | Ví dụ `CartService` |
| Side-effect tự động khi model thay đổi | `app/Observers/` (đăng ký trong `AppServiceProvider`) | Ví dụ trừ stock khi tạo Order |
| Interface cho nhiều implementation (thanh toán...) | `app/Contracts/` | Ví dụ `PaymentGateway` |
| Validate input | Form Request hoặc Livewire validation rules | KHÔNG viết rules trực tiếp trong controller |
| Query tái sử dụng | Eloquent Model scope | KHÔNG tạo Repository |
| UI component dùng chung | `resources/views/components/` | `x-button`, `x-input`, `x-container`, `x-product-card` |
| Tương tác động (filter, giỏ hàng, checkout) | `app/Livewire/` | Mỗi component 1 trách nhiệm rõ ràng |
| Admin CRUD | `app/Filament/Resources/` | Generate bằng `make:filament-resource` |

**Nguyên tắc tối quan trọng**: Controller/Livewire component chỉ điều hướng UI state và gọi Service/Action — KHÔNG chứa business logic.

## 3. Coding standards

- PSR-12. Class: PascalCase. Method/variable: camelCase. Table: snake_case số nhiều. Route name: kebab-case.dot (`products.show`, `cart.add`).
- Type hints + return types cho MỌI method (PHP 8 strict typing).
- Migration luôn có `down()`. Model dùng `$fillable` rõ ràng, không mở mass assignment toàn bộ.
- Giá tiền: `decimal(10,2)` — áp dụng nhất quán, không trộn integer/decimal giữa các bảng.
- Foreign key luôn có constraint (`onDelete` cascade/restrict tùy nghiệp vụ).
- Log lỗi quan trọng bằng `Log::error()` kèm context, KHÔNG log thông tin nhạy cảm (số thẻ, password, token).
- Không show raw exception ra UI khi `APP_DEBUG=false`.
- Commit message: `feat:`, `fix:`, `refactor:`, `docs:` — mỗi commit là 1 thay đổi logic hoàn chỉnh.

## 4. Design patterns — chỉ dùng khi cần

- **Action classes**: cho 1 hành động nghiệp vụ cụ thể (`AddToCartAction`, `PlaceOrderAction`). Mỗi class 1 method `execute()`.
- **Observer**: cho side-effect tự động lặp lại (trừ stock khi Order tạo, gửi email khi đổi trạng thái).
- **Strategy (PaymentGateway)**: chỉ tạo khi có ≥2 cổng thanh toán thật cần hỗ trợ. Interface trong `app/Contracts/`, mỗi cổng 1 class implement riêng — không if/else theo tên cổng trong controller.
- **KHÔNG dùng Repository pattern.** Eloquent + scope đã đủ. Nếu trong lúc code thấy "cần Repository" (ví dụ phải đổi nguồn dữ liệu), DỪNG LẠI và hỏi user trước khi thêm layer mới.
- Quy tắc chung: chọn pattern đơn giản nhất giải quyết đúng vấn đề hiện tại — không thêm abstraction "phòng khi cần sau này".

## 5. CSS & Responsive — mobile-first, dùng design tokens

### Color tokens (tailwind.config.js) — KHÔNG dùng màu Tailwind mặc định trực tiếp trong view

```js
colors: {
  primary:   { light: '#F3E8FF', DEFAULT: '#A855F7', dark: '#7E22CE' }, // tím
  secondary: { light: '#FCE7F3', DEFAULT: '#EC4899' },                   // hồng
  neutral:   { text: '#374151', muted: '#9CA3AF', bg: '#F9FAFB' },
}
```

Dùng `bg-primary`, `text-primary-dark`, `hover:bg-secondary`... Không viết `bg-purple-500`, `text-pink-400` trực tiếp.

### Breakpoint — Tailwind chuẩn, mobile-first, không tạo breakpoint custom

| Breakpoint | Width | Dùng cho |
|---|---|---|
| (default) | < 640px | Mobile |
| `sm:` | ≥ 640px | Tablet đứng |
| `md:` | ≥ 768px | Tablet ngang |
| `lg:` | ≥ 1024px | Desktop |
| `xl:` / `2xl:` | ≥ 1280px / 1536px | Desktop lớn |

KHÔNG dùng `max-width` queries.

### Quy ước layout bắt buộc

- **Product grid**: `grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6` — áp dụng mọi nơi hiển thị danh sách sản phẩm (trang chủ, `/products`, kết quả search).
- **Container**: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`.
- **Ảnh sản phẩm**: luôn `aspect-square object-cover rounded-lg` (trừ khi user yêu cầu tỉ lệ khác, ví dụ `aspect-[3/4]`).
- **Trang chi tiết sản phẩm**: `flex-col lg:flex-row`, ảnh và info chia `lg:w-1/2` mỗi bên.
- **Trang giỏ hàng/checkout**: `flex-col lg:flex-row`, danh sách `lg:w-2/3`, tóm tắt `lg:w-1/3`.
- **Heading**: `text-2xl lg:text-3xl` (h1), giảm dần cho h2/h3. Body: `text-sm md:text-base`.
- **Section spacing**: `py-8 lg:py-16`. Border radius nhất quán: `rounded-lg`/`rounded-xl` cho card/button/input — không trộn `rounded-md` và `rounded-2xl` tùy chỗ.

### Component dùng chung — bắt buộc tái sử dụng

- `<x-button variant="primary|secondary" size="sm|base">` — không viết class Tailwind cho button trực tiếp ở từng nơi.
- `<x-input>` — style border/focus nhất quán cho mọi form input/select.
- `<x-container>` — bọc nội dung mọi page.
- `<x-product-card :product="$product">` — dùng ở mọi nơi hiển thị sản phẩm dạng lưới.

### Header responsive

- Mobile: chỉ logo + icon giỏ hàng + hamburger (Alpine.js toggle menu dọc).
- Desktop (`lg:`): logo trái — menu ngang giữa — icon giỏ hàng phải, ẩn hamburger.

### Checklist trước khi coi 1 UI component/page là "xong"

Kiểm tra ở 375px, 768px, 1280px:
- Text không overflow/wrap xấu trên mobile
- Ảnh không méo (`object-cover`)
- Nút bấm ≥ 44px chiều cao trên mobile
- Header có hamburger trên mobile, menu ngang trên desktop

## 6. Filament admin — quy ước

- Mọi Resource đặt trong `app/Filament/Resources/`, generate bằng `make:filament-resource`.
- Bảng danh sách: luôn có search theo field chính (name/email), filter theo các field phân loại (category, status).
- Badge màu cho trạng thái Order: `pending` = amber, `shipped` = blue, `completed` = green, `cancelled` = red/gray.
- Resource chỉ chỉnh field hiển thị/form — KHÔNG đặt business logic trong Resource (gọi Action/Service nếu cần xử lý khi save).
- Resource chỉ-xem (ví dụ Customer): tắt `canCreate`, `canEdit`, `canDelete`.

## 7. Lệnh thường dùng (trong Docker)

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan make:livewire ComponentName
docker compose exec app php artisan make:filament-resource Name
docker compose exec app composer install
docker compose exec app npm run dev
docker compose exec app ./vendor/bin/pint   # format code
docker compose exec app ./vendor/bin/phpstan analyse  # static analysis
```

## 8. Bảo mật — luôn kiểm tra

- Không commit `.env`/`.env.production` (kiểm tra `.gitignore`).
- Không hardcode API key, password, S3 secret trong code — chỉ dùng biến môi trường, cập nhật `.env.example` làm template.
- `APP_DEBUG=false` trên production — không show raw exception cho user.

## 9. Khi không chắc

Nếu một yêu cầu có vẻ cần pattern/abstraction không có trong skill này (Repository, queue mới, package lớn...), dừng lại và hỏi user trước khi implement, thay vì tự quyết định và code luôn.