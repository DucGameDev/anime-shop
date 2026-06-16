# Anime Shop — Tài liệu phát triển

Project bán đồ anime (figure, áo, manga, sticker) dùng Laravel + Livewire + Filament + Docker + MySQL.

---

## 1. Tổng quan

- **Frontend khách hàng**: Laravel Blade + Livewire + Alpine.js + Tailwind CSS
- **Trang quản trị**: Filament (tại `/admin`)
- **Database**: MySQL (chạy trong Docker) — không dùng SQLite
- **Giỏ hàng**: lưu session, không cần đăng nhập
- **Production**: Dockerfile.prod multi-stage, ảnh sản phẩm lưu trên S3-compatible storage (S3/Spaces/R2)

---

## 2. Phiên bản stack thực tế (từ `composer.json`)

| Package | Phiên bản |
|---|---|
| PHP | ^8.2 |
| laravel/framework | ^12.0 |
| livewire/livewire | ^3.5 (KHÔNG dùng v4 — Filament v3 yêu cầu v3.5) |
| filament/filament | ^3.0 |
| league/flysystem-aws-s3-v3 | ^3.34 |
| laravel/breeze | ^2.4 (dev) |
| laravel/pint | ^1.24 (dev) |
| phpunit/phpunit | ^11.5 (dev) |

Frontend (từ `package.json`): Tailwind CSS v3, Alpine.js v3, Vite.

---

## 3. Cấu trúc thư mục — đặt code đúng chỗ

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

### File thực tế hiện có

**Actions** (`app/Actions/`):
- `PlaceOrderAction.php` — tạo Order, OrderItems, trừ stock (qua Observer), xoá giỏ hàng

**Services** (`app/Services/`):
- `CartService.php` — quản lý giỏ hàng lưu session (add, remove, qty, total, clear)

**Observers** (`app/Observers/`):
- `OrderItemObserver.php` — `created()` tự động `decrement('stock', qty)` trên Product

**Models** (`app/Models/`):
- `User.php` — implements FilamentUser; `orders()` liên kết qua `customer_email`
- `Product.php` — scopes: `scopeByCategory`, `scopeInStock`; accessor `getImageUrlAttribute()` xử lý cả URL đầy đủ (legacy) lẫn path lưu trên disk
- `Category.php` — `products(): HasMany`
- `Order.php` — scope `scopeByStatus()`; `items(): HasMany`
- `OrderItem.php` — `order(): BelongsTo`, `product(): BelongsTo`

**Livewire** (`app/Livewire/`):
- `CartIcon.php` — icon giỏ hàng với số lượng, lắng nghe event `cart-updated`
- `Cart.php` — mini cart
- `CartPage.php` — trang `/cart` đầy đủ: tăng/giảm qty, xóa sản phẩm
- `ProductList.php` — trang `/products`: search + filter theo category
- `Checkout.php` — form checkout với validation, gọi `PlaceOrderAction`

**Filament Resources** (`app/Filament/Resources/`):
- `ProductResource.php` — form có FileUpload ảnh (disk `public`/`s3` theo APP_ENV), Select category
- `CategoryResource.php` — xoá có guard (không xoá nếu còn sản phẩm)
- `OrderResource.php` — canCreate=false; badge trạng thái; chỉ cho sửa status
- `UserResource.php` — canCreate/Edit/Delete=false; thống kê đơn hàng; RelationManager orders
- `UserResource/RelationManagers/OrdersRelationManager.php` — isReadOnly=true

**Filament Widgets** (`app/Filament/Widgets/`):
- `StatsOverviewWidget.php` — 4 thẻ: đơn hôm nay, doanh thu hôm nay, đơn pending, sp sắp hết hàng
- `RevenueChartWidget.php` — line chart doanh thu 7 ngày, màu `#A855F7`
- `LatestOrdersWidget.php` — TableWidget 5 đơn mới nhất

**Blade components dùng chung** (`resources/views/components/`):
- `button.blade.php` — `variant="primary|secondary"`, `size="sm|base"`
- `input.blade.php` — style nhất quán cho mọi `<input>`/`<select>`
- `container.blade.php` — `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`
- `product-card.blade.php` — card sản phẩm dùng ở mọi lưới

---

## 4. Routes (`routes/web.php`)

```
GET  /                          → HomeController@index         (home)
GET  /products                  → view('products.index')       (products.index)
GET  /products/{product:slug}   → ProductController@show       (products.show)
GET  /cart                      → view('cart.index')           (cart.index)
POST /cart/{product}            → CartController@add           (cart.add)
GET  /checkout                  → view('checkout.index')       (checkout.index)
GET  /orders/{order}            → OrderController@show         (orders.show)
```

---

## 5. Database — migrations đã chạy

| Batch | Migration | Nội dung |
|---|---|---|
| 1 | sessions, users, password_reset_tokens, cache, jobs | Laravel defaults + Breeze |
| 2 | create_products_table | name, slug, description, price decimal(10,2), image_url, category(string), stock |
| 3 | create_orders_table | customer_name, phone, address, status(pending), total_amount decimal(10,2) |
| 3 | create_order_items_table | order_id FK, product_id FK, quantity, price decimal(10,2) |
| 3 | create_categories_table | id, name, slug — seed 4 danh mục trong `up()` |
| 3 | refactor_products_category_to_category_id | đổi string → FK, data migration kèm |
| 4 | add_customer_email_to_orders_table | nullable customer_email để link guest → User |

---

## 6. Coding standards

- PSR-12. Class: PascalCase. Method/variable: camelCase. Table: snake_case số nhiều. Route name: kebab-case.dot (`products.show`, `cart.add`).
- `declare(strict_types=1)` ở đầu mỗi file PHP.
- Type hints + return types cho MỌI method (PHP 8 strict typing).
- Migration luôn có `down()`. Model dùng `$fillable` rõ ràng, không mở mass assignment toàn bộ.
- Giá tiền: `decimal(10,2)` — áp dụng nhất quán, không trộn integer/decimal giữa các bảng.
- Foreign key luôn có constraint (`onDelete` cascade/restrict tùy nghiệp vụ).
- Log lỗi quan trọng bằng `Log::error()` kèm context, KHÔNG log thông tin nhạy cảm (số thẻ, password, token).
- Không show raw exception ra UI khi `APP_DEBUG=false`.
- Commit message: `feat:`, `fix:`, `refactor:`, `docs:` — mỗi commit là 1 thay đổi logic hoàn chỉnh.

---

## 7. Design patterns — chỉ dùng khi cần

- **Action classes**: cho 1 hành động nghiệp vụ cụ thể (`AddToCartAction`, `PlaceOrderAction`). Mỗi class 1 method `execute()`.
- **Observer**: cho side-effect tự động lặp lại (trừ stock khi Order tạo, gửi email khi đổi trạng thái).
- **Strategy (PaymentGateway)**: chỉ tạo khi có ≥2 cổng thanh toán thật. Interface trong `app/Contracts/`, mỗi cổng 1 class implement riêng — không if/else theo tên cổng trong controller.
- **KHÔNG dùng Repository pattern.** Eloquent + scope đã đủ. Nếu thấy "cần Repository", DỪNG LẠI và hỏi user trước.
- Quy tắc chung: chọn pattern đơn giản nhất giải quyết đúng vấn đề hiện tại — không thêm abstraction "phòng khi cần sau này".

---

## 8. CSS & Responsive — mobile-first, dùng design tokens

### Color tokens (tailwind.config.js) — KHÔNG dùng màu Tailwind mặc định trực tiếp trong view

```js
colors: {
  primary:   { light: '#F3E8FF', DEFAULT: '#A855F7', dark: '#7E22CE' }, // tím
  secondary: { light: '#FCE7F3', DEFAULT: '#EC4899' },                   // hồng
  neutral:   { text: '#374151', muted: '#9CA3AF', bg: '#F9FAFB' },
  info:      { DEFAULT: '#3B82F6' },   // blue — badge shipped
  warning:   { DEFAULT: '#F59E0B' },   // amber — badge pending
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

### Layout chuẩn

| Thành phần | Class |
|---|---|
| Container | `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` |
| Product grid | `grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6` |
| Ảnh sản phẩm | `aspect-square object-cover rounded-lg` |
| Trang chi tiết sản phẩm | `flex-col lg:flex-row`, mỗi bên `lg:w-1/2` |
| Giỏ hàng / checkout | `flex-col lg:flex-row`, list `lg:w-2/3`, tóm tắt `lg:w-1/3` |
| Heading h1 | `text-2xl lg:text-3xl font-bold` |
| Section spacing | `py-8 lg:py-16` |
| Border radius | `rounded-lg` cho card/button/input — nhất quán, không trộn `rounded-md` và `rounded-2xl` |

### Component dùng chung — bắt buộc tái sử dụng

- `<x-button variant="primary|secondary" size="sm|base">` — không viết class Tailwind cho button trực tiếp từng nơi.
- `<x-input>` — style border/focus nhất quán cho mọi form input/select.
- `<x-container>` — bọc nội dung mọi page.
- `<x-product-card :product="$product">` — dùng ở mọi nơi hiển thị sản phẩm dạng lưới.

### Header responsive

- **Mobile**: chỉ logo + icon giỏ hàng + hamburger (Alpine.js toggle menu dọc).
- **Desktop (`lg:`)**: logo trái — menu ngang giữa — icon giỏ hàng phải, ẩn hamburger.

### Checklist trước khi coi 1 UI component/page là "xong"

Kiểm tra ở 375px, 768px, 1280px:
- [ ] Text không overflow/wrap xấu trên mobile
- [ ] Ảnh không méo (`object-cover`)
- [ ] Nút bấm ≥ 44px chiều cao trên mobile
- [ ] Header có hamburger trên mobile, menu ngang trên desktop
- [ ] Chỉ dùng color tokens, không hardcode màu Tailwind mặc định
- [ ] Đã tái sử dụng component dùng chung nếu có sẵn

---

## 9. Filament admin — quy ước

- Mọi Resource đặt trong `app/Filament/Resources/`, generate bằng `make:filament-resource`.
- Bảng danh sách: luôn có search theo field chính (name/email), filter theo các field phân loại (category, status).
- Badge màu cho trạng thái Order: `pending` = warning/amber, `shipped` = info/blue, `completed` = success/green, `cancelled` = danger/red.
- Resource chỉ định nghĩa field hiển thị/form — KHÔNG đặt business logic trong Resource (gọi Action/Service nếu cần xử lý khi save).
- Resource chỉ-xem (Customer/User): tắt `canCreate`, `canEdit`, `canDelete`.
- Upload ảnh: dùng disk theo `APP_ENV` (`public` ở local, `s3` ở production) — không hardcode disk.

---

## 10. Storage — ảnh sản phẩm

- **Local**: disk `public` → `storage/app/public/products/`, symlink `public/storage`
- **Production**: disk `s3` → AWS S3 / DigitalOcean Spaces / Cloudflare R2
- `Product::getImageUrlAttribute()` xử lý cả hai: path lưu trên disk → `Storage::disk()->url()`, URL đầy đủ (legacy data) → trả thẳng
- Config trong `config/filesystems.php`, biến môi trường trong `.env.example`

---

## 11. Docker

### Services (docker-compose.yml)

| Service | Image | Port ngoài |
|---|---|---|
| app | PHP-FPM (custom) | — |
| nginx | nginx:alpine | 8005 |
| mysql | mysql:8.0 | 3307 |
| phpmyadmin | phpmyadmin | 8080 |

### Lệnh thường dùng

```bash
# Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan make:livewire ComponentName
docker compose exec app php artisan make:filament-resource Name

# Package management
docker compose exec app composer install
docker compose exec app npm run dev

# Code quality
docker compose exec app ./vendor/bin/pint           # format code (PSR-12)
docker compose exec app ./vendor/bin/phpstan analyse # static analysis

# Production build
docker compose exec app npm run build
```

### Production deploy — `Dockerfile.prod`

Multi-stage build:
1. **Stage node** (`node:20-alpine`): `npm ci && npm run build`
2. **Stage php** (`php:8.2-fpm-alpine`): extensions (pdo_mysql, redis, opcache, pcntl...) + `composer install --no-dev --optimize-autoloader` + copy assets + chown + HEALTHCHECK `php-fpm -t`

`docker/entrypoint.prod.sh` — chạy khi container start:
1. Đợi DB sẵn sàng (tối đa 30×2s qua `db:show`)
2. `php artisan migrate --force`
3. `php artisan storage:link --force`
4. `config:cache`, `route:cache`, `view:cache`
5. `filament:cache-components`
6. `exec "$@"` — hand off to PHP-FPM

---

## 12. Tình trạng tính năng hiện tại

| Tính năng | Trạng thái | Ghi chú |
|---|---|---|
| Danh mục sản phẩm (Category) | ✅ Xong | Model + migration + Filament Resource + guard xóa |
| Trang danh sách sản phẩm | ✅ Xong | Livewire filter/search, grid responsive |
| Trang chi tiết sản phẩm | ✅ Xong | ProductController@show |
| Giỏ hàng (session) | ✅ Xong | CartService + CartPage Livewire + CartIcon |
| Checkout + đặt hàng | ✅ Xong | PlaceOrderAction + OrderItemObserver trừ stock |
| Trang xác nhận đơn hàng | ✅ Xong | OrderController@show, badge trạng thái |
| Admin: Product | ✅ Xong | FileUpload ảnh, Select category, search/filter |
| Admin: Category | ✅ Xong | Guard xóa, đếm số sản phẩm |
| Admin: Order | ✅ Xong | Badge status, chỉ sửa trạng thái |
| Admin: User | ✅ Xong | Chỉ xem, thống kê đơn + doanh thu, RelationManager |
| Dashboard widgets | ✅ Xong | StatsOverview + RevenueChart + LatestOrders |
| S3 storage config | ✅ Xong | Cần điền biến môi trường khi deploy |
| Dockerfile.prod | ✅ Xong | Multi-stage, entrypoint.prod.sh |
| Thanh toán online | ❌ Chưa làm | Implement khi có ≥2 cổng, dùng PaymentGateway interface |
| Gửi email xác nhận đơn | ❌ Chưa làm | Observer sẵn sàng, cần thêm Mailable + job |
| Đánh giá sản phẩm | ❌ Chưa làm | — |

---

## 13. Bảo mật

- Không commit `.env` / `.env.production` (kiểm tra `.gitignore`).
- Không hardcode API key, password, S3 secret trong code — chỉ dùng biến môi trường, cập nhật `.env.example` làm template.
- `APP_DEBUG=false` trên production — không show raw exception cho user.
- `APP_KEY` production: generate bằng `php artisan key:generate --show`, lưu vào CI/CD secret, KHÔNG commit.
- `SESSION_ENCRYPT=true` + `SESSION_SECURE_COOKIE=true` trên production.
- `Log::error()` kèm context cho lỗi nghiêm trọng — KHÔNG log số thẻ, password, token.
- Filament security advisories đã acknowledged trong `composer.json` → `config.audit.ignore`.

---

## 14. Agents có sẵn

| Agent | Dùng khi |
|---|---|
| `laravel-backend` | Migration, Model, Service, Action, Observer, Form Request, route, business logic |
| `ui-developer` | Blade view, Livewire markup, Tailwind CSS, component dùng chung, responsive layout |
| `filament-admin` | Filament Resource, Dashboard widget, form/table/filter trong `/admin` |
| `code-reviewer` | Review code sau khi hoàn thành tính năng, trước khi commit (chỉ đọc, không sửa) |
| `deploy-ops` | Docker, Dockerfile.prod, docker-compose, `.env.example`, S3 config, backup script |

---

## 15. Khi không chắc

Nếu một yêu cầu có vẻ cần pattern/abstraction không có trong tài liệu này (Repository, queue mới, package lớn...), dừng lại và hỏi user trước khi implement.
