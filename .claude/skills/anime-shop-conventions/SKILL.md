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
| Side-effect tự động khi model thay đổi | `app/Observers/` (đăng ký trong `AppServiceProvider::boot()`) | Ví dụ trừ stock khi tạo Order |
| Interface cho nhiều implementation (thanh toán...) | `app/Contracts/` | Ví dụ `PaymentGateway` |
| Validate input | Form Request hoặc Livewire validation rules | KHÔNG viết rules trực tiếp trong controller |
| Query tái sử dụng | Eloquent Model scope | KHÔNG tạo Repository |
| UI component dùng chung | `resources/views/components/` | `x-button`, `x-input`, `x-container`, `x-product-card` |
| Tương tác động (filter, giỏ hàng, checkout) | `app/Livewire/` | Mỗi component 1 trách nhiệm rõ ràng |
| Admin CRUD | `app/Filament/Resources/` | Generate bằng `make:filament-resource` |

**Nguyên tắc tối quan trọng**: Controller/Livewire component chỉ điều hướng UI state và gọi Service/Action — KHÔNG chứa business logic.

## 3. Coding standards

- PSR-12. Class: PascalCase. Method/variable: camelCase. Table: snake_case số nhiều. Route name: kebab-case.dot (`products.show`, `cart.add`).
- `declare(strict_types=1)` ở đầu mỗi file PHP.
- Type hints + return types cho MỌI method (PHP 8 strict typing).
- Migration luôn có `down()`. Model dùng `$fillable` rõ ràng, không mở mass assignment toàn bộ.
- Giá tiền: `decimal(10,2)` — áp dụng nhất quán, không trộn integer/decimal giữa các bảng.
- Foreign key luôn có constraint (`onDelete` cascade/restrict tùy nghiệp vụ).
- Log lỗi quan trọng bằng `Log::error()` kèm context, KHÔNG log thông tin nhạy cảm (số thẻ, password, token).
- Không show raw exception ra UI khi `APP_DEBUG=false`.
- Commit message: `feat:`, `fix:`, `refactor:`, `docs:` — mỗi commit là 1 thay đổi logic hoàn chỉnh.

## 4. Design patterns — chỉ dùng khi cần

- **Action classes**: cho 1 hành động nghiệp vụ cụ thể (`PlaceOrderAction`, `ImportProductsAction`). Mỗi class 1 method `execute()`.
- **Observer**: cho side-effect tự động lặp lại (trừ stock khi Order tạo, gửi email khi đổi trạng thái). Đăng ký trong `AppServiceProvider::boot()` bằng `Model::observe()` — không dùng `#[ObservedBy]` attribute.
- **Strategy (PaymentGateway)**: chỉ tạo khi có ≥2 cổng thanh toán thật cần hỗ trợ. Interface trong `app/Contracts/`, mỗi cổng 1 class implement riêng — không if/else theo tên cổng trong controller.
- **KHÔNG dùng Repository pattern.** Eloquent + scope đã đủ. Nếu trong lúc code thấy "cần Repository" (ví dụ phải đổi nguồn dữ liệu), DỪNG LẠI và hỏi user trước khi thêm layer mới.
- Quy tắc chung: chọn pattern đơn giản nhất giải quyết đúng vấn đề hiện tại — không thêm abstraction "phòng khi cần sau này".

## 5. Business rules — phải biết trước khi code

**Đánh giá sản phẩm (Review):**
- Chỉ được submit khi order có status `completed` VÀ order đó chứa sản phẩm muốn review.
- Kiểm tra qua `Order::hasProductReviewedBy($productId, $userId)`.

**Voucher:**
- Luôn dùng `Voucher::isValid()` trước khi áp dụng (check active, expired, max_uses).
- `calculateDiscount($orderTotal)` kiểm tra `min_order` và trả số tiền giảm thực tế.
- Sau khi order hoàn tất: `Voucher::increment('used_count')`.

**Guest checkout:**
- Order lưu `customer_email`, không có `user_id`.
- `User::orders()` liên kết qua `customer_email` — guest orders tự xuất hiện trong tài khoản nếu đăng ký cùng email.
- Không dùng `Order::where('user_id', ...)` — cột này không tồn tại.

**Stock:**
- `PlaceOrderAction` verify stock từ DB (lockForUpdate) trước khi tạo order — không tin giá/stock lưu trong session.
- Trừ stock diễn ra trong `OrderItemObserver::created()`, không trực tiếp trong Action.

**SoftDeletes trên Product:**
- Query mặc định exclude deleted products.
- Khi hiển thị OrderItem cũ: dùng `$item->product()->withTrashed()->first()` để tránh null.

**Order status hợp lệ:** `unpaid` → `pending` → `shipped` → `completed` / `cancelled`

**Phân quyền User:** `User::ROLE_ADMIN` / `User::ROLE_CUSTOMER` — check bằng `isAdmin()` / `isCustomer()`. Filament chỉ cho `isAdmin()` vào.

## 6. Auth guards — `web` vs `admin`

Project có 2 guard tách biệt:
- **`web`** — khách hàng (Breeze), đăng nhập tại `/login`
- **`admin`** — Filament admin, đăng nhập tại `/admin/login`

```php
// Đúng — route tài khoản khách hàng
Route::middleware('auth')->group(function () { ... });

// Sai — nhầm guard, khách hàng bị từ chối dù đã đăng nhập
Route::middleware('auth:admin')->group(function () { ... });
```

Route thuộc Filament không cần đặt middleware thủ công — panel tự xử lý.

## 7. Livewire events

| Event | Dispatch từ | Lắng nghe bởi |
|---|---|---|
| `cart-updated` | `CartPage`, `Cart` | `CartIcon` |

Bất kỳ component nào thay đổi `CartService` phải dispatch `cart-updated` để `CartIcon` đồng bộ số lượng.

## 8. CSS & Responsive — mobile-first, dùng design tokens

### Color tokens (tailwind.config.js) — KHÔNG dùng màu Tailwind mặc định trực tiếp trong view

```js
colors: {
  primary:   { light: '#F3E8FF', DEFAULT: '#A855F7', dark: '#7E22CE' }, // tím
  secondary: { light: '#FCE7F3', DEFAULT: '#EC4899' },                   // hồng
  neutral:   { text: '#374151', muted: '#9CA3AF', bg: '#F9FAFB' },
  info:      { DEFAULT: '#3B82F6' },
  warning:   { DEFAULT: '#F59E0B' },
}
```

Dùng `bg-primary`, `text-primary-dark`, `hover:bg-secondary`... Không viết `bg-purple-500`, `text-pink-400` trực tiếp.

### Breakpoint — Tailwind chuẩn, mobile-first, không tạo breakpoint custom

| Breakpoint | Width |
|---|---|
| (default) | < 640px — Mobile |
| `sm:` | ≥ 640px |
| `md:` | ≥ 768px |
| `lg:` | ≥ 1024px — Desktop |

KHÔNG dùng `max-width` queries.

### Quy ước layout bắt buộc

- **Product grid**: `grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6`
- **Container**: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`
- **Ảnh sản phẩm**: `aspect-square object-cover rounded-lg`
- **Trang chi tiết sản phẩm**: `flex-col lg:flex-row`, ảnh và info `lg:w-1/2` mỗi bên
- **Giỏ hàng/checkout**: `flex-col lg:flex-row`, danh sách `lg:w-2/3`, tóm tắt `lg:w-1/3`
- **Heading**: `text-2xl lg:text-3xl` (h1). Section spacing: `py-8 lg:py-16`
- **Border radius**: `rounded-lg` nhất quán — không trộn `rounded-md` và `rounded-2xl`

### Component dùng chung — bắt buộc tái sử dụng

- `<x-button variant="primary|secondary" size="sm|base">` — không viết class Tailwind cho button trực tiếp.
- `<x-input>` — style nhất quán cho mọi form input/select.
- `<x-container>` — bọc nội dung mọi page.
- `<x-product-card :product="$product">` — mọi nơi hiển thị sản phẩm dạng lưới.
- `<x-account-layout>` — layout sidebar cho các trang `/account/*`.

### Header responsive

- Mobile: logo + icon giỏ hàng + hamburger (Alpine.js toggle menu dọc).
- Desktop (`lg:`): logo trái — menu ngang giữa — icon giỏ hàng phải.

### Checklist trước khi coi 1 UI component/page là "xong"

Kiểm tra ở 375px, 768px, 1280px:
- Text không overflow/wrap xấu trên mobile
- Ảnh không méo (`object-cover`)
- Nút bấm ≥ 44px chiều cao trên mobile
- Header có hamburger trên mobile, menu ngang trên desktop
- Chỉ dùng color tokens, không hardcode màu Tailwind mặc định

## 9. Filament admin — quy ước

- Mọi Resource đặt trong `app/Filament/Resources/` — panel tự discover, không cần đăng ký thủ công.
- Bảng danh sách: luôn có search theo field chính (name/email), filter theo field phân loại.
- Badge màu Order: `unpaid` = gray, `pending` = amber, `shipped` = blue, `completed` = green, `cancelled` = red.
- Resource chỉ định nghĩa field — KHÔNG đặt business logic trong Resource.
- Resource chỉ-xem: tắt `canCreate`, `canEdit`, `canDelete`.
- Upload ảnh: disk theo `APP_ENV` (`public` ở local, `s3` ở production) — không hardcode disk.
- Panel ID: `admin`, auth guard: `admin`, theme: Amber.

## 10. Gotchas thường gặp

**Disk phụ thuộc `APP_ENV`, không phải `APP_DEBUG`:**
```php
$disk = app()->environment('production') ? 's3' : 'public';
```

**`welcome.blade.php` ≠ trang chủ:**
Trang chủ thực tế là `resources/views/home.blade.php` → `HomeController@index`. `welcome.blade.php` là file mặc định Laravel, không được dùng.

**SoftDeletes + OrderItem:**
```php
// Tránh null khi product bị xóa mềm
$item->product()->withTrashed()->first()
```

**CartIcon không cập nhật:** Quên dispatch `cart-updated` sau khi thay đổi giỏ hàng.

**`ProductList` sort values hợp lệ:** `newest`, `price_asc`, `price_desc`, `popular`, `random`.

**Migration categories:** Seed 4 danh mục (`figure`, `ao`, `manga`, `sticker`) ngay trong `up()` — không đổi slug, `ProductSeeder` phụ thuộc vào đây.

## 11. Lệnh thường dùng (trong Docker)

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan make:livewire ComponentName
docker compose exec app php artisan make:filament-resource Name
docker compose exec app composer install
docker compose exec app npm run dev
docker compose exec app ./vendor/bin/pint           # format code (PSR-12)
docker compose exec app ./vendor/bin/phpstan analyse # static analysis
docker compose exec app php artisan test
```

## 12. Bảo mật — luôn kiểm tra

- Không commit `.env`/`.env.production` (kiểm tra `.gitignore`).
- Không hardcode API key, password, S3 secret trong code — chỉ dùng biến môi trường.
- `APP_DEBUG=false` trên production — không show raw exception cho user.
- Checkout có honeypot + reCAPTCHA + rate limiter (5 lần/phút) — không bỏ khi sửa Checkout component.

## 13. Khi không chắc

Nếu một yêu cầu có vẻ cần pattern/abstraction không có trong skill này (Repository, queue mới, package lớn...), dừng lại và hỏi user trước khi implement, thay vì tự quyết định và code luôn.
