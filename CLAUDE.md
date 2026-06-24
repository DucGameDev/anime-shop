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
- `PlaceOrderAction.php` — tạo Order, OrderItems, verify giá/stock từ DB, trừ stock (qua Observer), hỗ trợ voucher, xoá giỏ hàng
- `ImportProductsAction.php` — nhập sản phẩm hàng loạt từ file XLSX, validate từng dòng, tạo slug duy nhất

**Services** (`app/Services/`):
- `CartService.php` — quản lý giỏ hàng lưu session: `addItem()`, `removeItem()`, `updateQuantity()`, `clearCart()`, `getTotal()`, `getItemCount()`

**Observers** (`app/Observers/`):
- `OrderItemObserver.php` — `created()` tự động `decrement('stock', qty)` trên Product

**Models** (`app/Models/`):
- `User.php` — const `ROLE_ADMIN`, `ROLE_CUSTOMER`; `isAdmin()`, `isCustomer()`; implements FilamentUser; relations: `orders()` (liên kết qua `customer_email`), `addresses()`, `favorites()`
- `Product.php` — SoftDeletes; scopes: `scopeByCategory($slug)`, `scopeInStock()`; accessor `getImageUrlAttribute()` xử lý URL đầy đủ (legacy) lẫn path trên disk; relations: `category()`, `orderItems()`, `reviews()`, `favoritedBy()`; method `averageRating()`
- `Category.php` — `products(): HasMany`
- `Order.php` — fields: `customer_name`, `customer_email`, `phone`, `address`, `note`, `payment_method`, `status`, `total_amount`, `voucher_code`, `discount_amount`; scope `scopeByStatus()`; relations: `user()` (qua `customer_email`), `items()`; method `hasProductReviewedBy($productId, $userId)`
- `OrderItem.php` — `order(): BelongsTo`, `product(): BelongsTo`
- `Review.php` — fields: `product_id`, `user_id`, `order_id`, `rating`, `comment`; relations: `product()`, `user()`, `order()`
- `Address.php` — fields: `user_id`, `label`, `recipient_name`, `phone`, `address`, `is_default`; relation: `user()`
- `Voucher.php` — fields: `code`, `type` (percent/fixed), `value`, `min_order`, `max_uses`, `used_count`, `expires_at`, `is_active`; methods: `isValid()`, `calculateDiscount($orderTotal)`

**Livewire** (`app/Livewire/`):
- `CartIcon.php` — icon giỏ hàng với số lượng, lắng nghe event `cart-updated`
- `Cart.php` — mini cart
- `CartPage.php` — trang `/cart` đầy đủ: tăng/giảm qty, xóa sản phẩm
- `ProductList.php` — trang `/products`: search, filter category, sort (newest/price_asc/price_desc/popular/random)
- `Checkout.php` — form checkout với validation, honeypot, reCAPTCHA, rate limiter 5/phút, áp voucher, address book, gọi `PlaceOrderAction`
- `ProductReviews.php` — hiển thị đánh giá + submit review (kiểm tra user đã mua sản phẩm và đơn đã hoàn thành)
- `FavoriteButton.php` — toggle yêu thích sản phẩm (yêu cầu đăng nhập)
- `AccountProfile.php` — sửa thông tin cá nhân + đổi mật khẩu
- `AccountAddresses.php` — CRUD địa chỉ giao hàng, set địa chỉ mặc định

**Controllers** (`app/Http/Controllers/`):
- `HomeController.php` — `index()`: sản phẩm tồn kho, shuffle, paginate 16
- `ProductController.php` — `show($product)`: 4 sản phẩm gợi ý (cùng category, tồn kho, random)
- `OrderController.php` — `show($order)`: verify quyền (auth + email match hoặc session `last_order_id`)
- `CartController.php` — `add($product)`: validate quantity, gọi `CartService->addItem()`, trả JSON
- `AccountController.php` — `orders()`, `profile()`, `addresses()`, `favorites()`
- `StaticPageController.php` — `orderGuide()`, `payment()`, `shipping()`, `returns()`
- `SitemapController.php` — generate XML sitemap

**Filament Resources** (`app/Filament/Resources/`):
- `ProductResource.php` — form: name, slug (auto), price, category, stock, description, image upload (disk theo APP_ENV); import XLSX từ `ImportProductsAction`
- `CategoryResource.php` — guard xóa (không xóa nếu còn sản phẩm)
- `OrderResource.php` — canCreate=false; badge trạng thái; chỉ sửa status; RelationManager `OrderItemsRelationManager` (read-only)
- `UserResource.php` — canCreate/Edit/Delete=false; thống kê đơn; RelationManager `OrdersRelationManager` (read-only)
- `AdminUserResource.php` — CRUD tài khoản admin (role=ROLE_ADMIN)
- `VoucherResource.php` — form: code (uppercase), type (percent/fixed), value, min_order, max_uses, expires_at, is_active; filter active/expired

**Filament Widgets** (`app/Filament/Widgets/`):
- `StatsOverviewWidget.php` — 9 thẻ: đơn hôm nay (sparkline 7 ngày), doanh thu hôm nay, đơn tháng, doanh thu tháng, đơn pending, khách hàng, sắp hết hàng, hết hàng, tổng sản phẩm
- `RevenueChartWidget.php` — line chart doanh thu 7 ngày, màu `#A855F7`
- `LatestOrdersWidget.php` — TableWidget 5 đơn mới nhất
- `OrderStatusChartWidget.php` — chart phân bổ trạng thái đơn hàng
- `RecentActivityWidget.php` — activity log gần đây

**Blade components dùng chung** (`resources/views/components/`):
- `button.blade.php` — `variant="primary|secondary"`, `size="sm|base"`
- `input.blade.php` — style nhất quán cho mọi `<input>`/`<select>`
- `container.blade.php` — `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`
- `product-card.blade.php` — card sản phẩm dùng ở mọi lưới
- `account-layout.blade.php` — layout trang tài khoản (sidebar + content)

---

## 4. Routes (`routes/web.php`)

```
GET  /                               → HomeController@index
GET  /products                       → view('products.index')
GET  /products/{product:slug}        → ProductController@show
GET  /cart                           → view('cart.index')
POST /cart/{product}                 → CartController@add
GET  /checkout                       → view('checkout.index')
GET  /orders/{order}                 → OrderController@show

# Trang tĩnh
GET  /huong-dan-dat-hang             → StaticPageController@orderGuide
GET  /hinh-thuc-thanh-toan           → StaticPageController@payment
GET  /chinh-sach-van-chuyen          → StaticPageController@shipping
GET  /chinh-sach-doi-tra             → StaticPageController@returns

# Tài khoản (auth required)
GET  /account/orders                 → AccountController@orders
GET  /account/profile                → AccountController@profile
GET  /account/addresses              → AccountController@addresses
GET  /account/favorites              → AccountController@favorites

# Breeze profile
GET|PATCH|DELETE /profile            → ProfileController

# Admin import template
GET  /admin/products/import-template → download file XLSX mẫu

# Sitemap
GET  /sitemap.xml                    → SitemapController@index
```

---

## 5. Database — migrations đã chạy

**Core tables:**
| Migration | Nội dung |
|---|---|
| `create_users_table` | Laravel default + Breeze |
| `create_sessions/cache/jobs_table` | Laravel default |
| `create_products_table` | name, slug, description, price decimal(10,2), image_url, stock, category_id FK |
| `create_categories_table` | id, name, slug — seed 4 danh mục |
| `create_orders_table` | customer_name, phone, address, status(pending), total_amount decimal(10,2) |
| `create_order_items_table` | order_id FK, product_id FK, quantity, price decimal(10,2) |
| `create_reviews_table` | product_id FK, user_id FK, order_id FK, rating, comment |
| `create_addresses_table` | user_id FK, label, recipient_name, phone, address, is_default |
| `create_favorites_table` | user_id FK, product_id FK (unique pair) |
| `create_vouchers_table` | code unique, type enum(percent/fixed), value, min_order, max_uses, used_count, expires_at, is_active |

**Modifier migrations:**
| Migration | Thay đổi |
|---|---|
| `refactor_products_category_to_category_id` | đổi string category → FK category_id |
| `add_customer_email_to_orders` | nullable customer_email để link guest → User |
| `add_soft_delete_to_products` | thêm `deleted_at` cho Product |
| `add_role_to_users` | thêm `role` (default: customer) |
| `add_phone_address_to_users` | thêm phone, address cho User |
| `add_note_to_orders` | thêm `note` nullable |
| `add_unpaid_status` | thêm status `unpaid` vào enum |
| `add_payment_method_to_orders` | thêm `payment_method` nullable |
| `add_discount_fields_to_orders` | thêm `voucher_code`, `discount_amount` |

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

- **Action classes**: cho 1 hành động nghiệp vụ cụ thể (`PlaceOrderAction`, `ImportProductsAction`). Mỗi class 1 method `execute()`/`handle()`.
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
- `<x-account-layout>` — layout sidebar cho các trang tài khoản.

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
- Badge màu cho trạng thái Order: `pending` = warning/amber, `unpaid` = gray, `shipped` = info/blue, `completed` = success/green, `cancelled` = danger/red.
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
| Trang danh sách sản phẩm | ✅ Xong | Livewire filter/search/sort, grid responsive |
| Trang chi tiết sản phẩm | ✅ Xong | ProductController@show + gợi ý sản phẩm |
| Giỏ hàng (session) | ✅ Xong | CartService + CartPage Livewire + CartIcon |
| Checkout + đặt hàng | ✅ Xong | PlaceOrderAction + Observer trừ stock + honeypot + reCAPTCHA + rate limiter |
| Voucher / mã giảm giá | ✅ Xong | Model Voucher + áp dụng trong Checkout + Admin VoucherResource |
| Trang xác nhận đơn hàng | ✅ Xong | OrderController@show, badge trạng thái |
| Đánh giá sản phẩm | ✅ Xong | Model Review + Livewire ProductReviews + kiểm tra đã mua |
| Yêu thích sản phẩm | ✅ Xong | Model + Livewire FavoriteButton + trang /account/favorites |
| Trang tài khoản | ✅ Xong | profile, đổi mật khẩu, địa chỉ, lịch sử đơn hàng |
| Trang tĩnh | ✅ Xong | hướng dẫn đặt hàng, thanh toán, vận chuyển, đổi trả |
| Sitemap XML | ✅ Xong | SitemapController, tự động include products/categories |
| Admin: Product | ✅ Xong | FileUpload ảnh, Select category, search/filter, import XLSX |
| Admin: Category | ✅ Xong | Guard xóa, đếm số sản phẩm |
| Admin: Order | ✅ Xong | Badge status (incl. unpaid), chỉ sửa trạng thái |
| Admin: User | ✅ Xong | Chỉ xem, thống kê đơn + doanh thu, RelationManager |
| Admin: AdminUser | ✅ Xong | CRUD tài khoản admin |
| Admin: Voucher | ✅ Xong | CRUD voucher, filter active/expired |
| Dashboard widgets | ✅ Xong | 9 stat cards + RevenueChart + LatestOrders + OrderStatusChart + RecentActivity |
| S3 storage config | ✅ Xong | Cần điền biến môi trường khi deploy |
| Dockerfile.prod | ✅ Xong | Multi-stage, entrypoint.prod.sh |
| Thanh toán online | ❌ Chưa làm | Implement khi có ≥2 cổng, dùng PaymentGateway interface |
| Gửi email xác nhận đơn | ❌ Chưa làm | Observer sẵn sàng, cần thêm Mailable + job |

---

## 13. Bảo mật

- Không commit `.env` / `.env.production` (kiểm tra `.gitignore`).
- Không hardcode API key, password, S3 secret trong code — chỉ dùng biến môi trường, cập nhật `.env.example` làm template.
- `APP_DEBUG=false` trên production — không show raw exception cho user.
- `APP_KEY` production: generate bằng `php artisan key:generate --show`, lưu vào CI/CD secret, KHÔNG commit.
- `SESSION_ENCRYPT=true` + `SESSION_SECURE_COOKIE=true` trên production.
- `Log::error()` kèm context cho lỗi nghiêm trọng — KHÔNG log số thẻ, password, token.
- Filament security advisories đã acknowledged trong `composer.json` → `config.audit.ignore`.
- Checkout có honeypot + reCAPTCHA + rate limiter (5 lần/phút) để chống spam.

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

---

## 16. Business rules — quy tắc nghiệp vụ quan trọng

Những quy tắc này không đọc ra được từ tên file — phải biết trước khi code liên quan.

**Đánh giá sản phẩm (Review):**
- User chỉ được submit review khi order của họ có status `completed` VÀ order đó chứa sản phẩm muốn review.
- Kiểm tra qua `Order::hasProductReviewedBy($productId, $userId)`.
- Mỗi user chỉ review 1 lần cho 1 sản phẩm trong 1 order — không duplicate.

**Voucher:**
- Luôn dùng `Voucher::isValid()` để kiểm tra trước khi áp dụng (active, chưa hết hạn, chưa đạt max_uses).
- `calculateDiscount($orderTotal)` kiểm tra `min_order` và trả về số tiền giảm thực tế.
- `type = percent` giảm theo %, `type = fixed` giảm số tiền cố định.
- Sau khi order hoàn tất: tăng `used_count` trên Voucher.

**Guest checkout & liên kết User:**
- Checkout không yêu cầu đăng nhập. Order lưu `customer_email`.
- `User::orders()` dùng `hasMany` liên kết qua `customer_email` thay vì `user_id` — guest orders tự động hiện trong tài khoản nếu user đăng ký cùng email.
- `OrderController::show` cho phép guest xem đơn qua `session('last_order_id')` (được set bởi `PlaceOrderAction`).

**Stock:**
- `PlaceOrderAction` verify stock từ DB ngay trước khi tạo order (không tin session cart).
- Trừ stock diễn ra trong `OrderItemObserver::created()` — không trừ trực tiếp trong Action.
- `Product::scopeInStock()` lọc `stock > 0`.

**SoftDeletes trên Product:**
- Query mặc định đã exclude deleted products (Eloquent tự xử lý).
- Nếu cần include: dùng `Product::withTrashed()`.
- Admin có thể xóa mềm sản phẩm — sản phẩm trong order cũ vẫn liên kết được qua `withTrashed`.

**Phân quyền User:**
- `User::ROLE_ADMIN` / `User::ROLE_CUSTOMER` — check bằng `$user->isAdmin()` / `$user->isCustomer()`.
- Filament chỉ cho `isAdmin()` truy cập (`canAccessPanel()`).
- `AdminUserResource` quản lý tài khoản có `role = ROLE_ADMIN`.

---

## 17. Livewire events — ai dispatch, ai lắng nghe

| Event | Dispatch từ | Lắng nghe bởi | Mục đích |
|---|---|---|---|
| `cart-updated` | `CartPage`, `Cart` | `CartIcon` | Cập nhật số lượng trên icon giỏ hàng |

**Quy tắc:** Bất kỳ component nào thay đổi giỏ hàng phải dispatch `cart-updated` để `CartIcon` đồng bộ số lượng. Nếu tạo component mới có thao tác với `CartService`, nhớ dispatch event này.

---

## 18. Order status flow

**Các trạng thái hợp lệ:**

```
unpaid → pending → shipped → completed
                → cancelled
pending         → cancelled
```

| Status | Màu badge | Ý nghĩa |
|---|---|---|
| `unpaid` | gray | Đã tạo đơn nhưng chưa thanh toán (COD chưa confirm) |
| `pending` | amber | Đã nhận đơn, đang xử lý |
| `shipped` | blue | Đã giao vận chuyển |
| `completed` | green | Giao thành công |
| `cancelled` | red | Đã hủy |

**Lưu ý:** Chỉ Admin mới được đổi status (qua `OrderResource`). Không có tự động chuyển status — tất cả thủ công. Review chỉ unlock khi status = `completed`.

---

## 19. Environment variables quan trọng

Ngoài các biến Laravel mặc định, project dùng thêm:

| Biến | Ảnh hưởng | Ghi chú |
|---|---|---|
| `APP_ENV` | Disk upload ảnh: `local` → disk `public`; `production` → disk `s3` | Logic trong `ProductResource` và `Product::getImageUrlAttribute()` |
| `RECAPTCHA_SITE_KEY` | Checkout form (frontend) | Cần cả hai key để reCAPTCHA hoạt động |
| `RECAPTCHA_SECRET_KEY` | Checkout validation (backend) | |
| `AWS_ACCESS_KEY_ID` | S3 storage | Dùng cho production |
| `AWS_SECRET_ACCESS_KEY` | S3 storage | |
| `AWS_DEFAULT_REGION` | S3 storage | |
| `AWS_BUCKET` | S3 storage | |
| `AWS_ENDPOINT` | S3 storage | Dùng cho Spaces/R2 (không phải AWS thuần) |
| `AWS_USE_PATH_STYLE_ENDPOINT` | S3 storage | `true` cho Cloudflare R2 |

Template đầy đủ trong `.env.example`.

---

## 20. Seeder — dữ liệu mẫu

```bash
# Chạy toàn bộ seeder (xóa data cũ + seed lại)
docker compose exec app php artisan migrate:fresh --seed

# Chỉ seed không migrate
docker compose exec app php artisan db:seed
```

**Seeder có sẵn:**

| Seeder | Nội dung |
|---|---|
| `DatabaseSeeder` | Gọi `ProductSeeder` + tạo 1 user test (`test@example.com`) |
| `ProductSeeder` | 12 sản phẩm mẫu: 3 figure, 3 áo, 3 manga, 3 sticker — dùng `placehold.co` làm ảnh |
| `OrderSeeder` | Tạo đơn hàng mẫu cho testing |

**Lưu ý:** `create_categories_table` migration tự seed 4 danh mục (`figure`, `ao`, `manga`, `sticker`) trong `up()` — không có seeder riêng cho Category. `ProductSeeder` phụ thuộc Categories đã tồn tại trong DB.

**Tài khoản test:**
- User: `test@example.com` / `password`
- Admin: tạo qua `php artisan make:filament-user` hoặc trực tiếp trong `AdminUserResource`

---

## 21. Gotchas thường gặp

**Disk upload phụ thuộc `APP_ENV`, không phải `APP_DEBUG`:**
```php
// ProductResource.php — đúng
$disk = app()->environment('production') ? 's3' : 'public';
```
Khi test local với `APP_ENV=local`, ảnh luôn lưu vào disk `public` dù `APP_DEBUG=false`.

**`customer_email` là cầu nối guest ↔ User:**
Không dùng `user_id` trong bảng `orders`. Nếu cần query orders của user:
```php
// Đúng — dùng relation đã định nghĩa
$user->orders()
// Sai — không có cột này
Order::where('user_id', $user->id)
```

**SoftDeletes và OrderItem:**
`OrderItem::product()` có thể trả `null` nếu product bị soft-delete mà không dùng `withTrashed`. Khi hiển thị order cũ, luôn dùng:
```php
$item->product()->withTrashed()->first()
// hoặc eager load: ->with(['items.product' => fn($q) => $q->withTrashed()])
```

**Livewire + CartIcon không cập nhật:**
Nếu viết component mới thay đổi CartService mà CartIcon không cập nhật → quên dispatch `cart-updated`.

**`ProductList` sort values hợp lệ:**
`newest`, `price_asc`, `price_desc`, `popular`, `random` — không thêm giá trị khác nếu không có xử lý trong component.

**Migration categories seed trong `up()`:**
Nếu rollback + re-migrate, categories bị seed lại. `ProductSeeder` phụ thuộc slug `figure`, `ao`, `manga`, `sticker` — không đổi slug categories.

---

## 22. Chạy tests

**Cấu hình:** `phpunit.xml` dùng SQLite in-memory cho test environment — không cần Docker MySQL khi chạy test.

```bash
# Chạy tất cả tests
docker compose exec app php artisan test

# Chạy test cụ thể
docker compose exec app php artisan test --filter RegistrationTest

# Chạy với PHPUnit trực tiếp
docker compose exec app ./vendor/bin/phpunit

# Xem coverage (nếu có Xdebug)
docker compose exec app ./vendor/bin/phpunit --coverage-html coverage/
```

**Tests hiện có:** Chủ yếu là Breeze auth tests (Authentication, Registration, PasswordReset, EmailVerification, Profile). Chưa có tests cho business logic của shop.

**Lưu ý:** Test dùng SQLite in-memory — nếu viết test mới cần tránh MySQL-specific syntax (ví dụ `JSON_CONTAINS`).

---

## 23. Cấu trúc Views — mapping đầy đủ

### Page views → Controller/Route

| View file | Route / Controller | Livewire component nhúng |
|---|---|---|
| `views/home.blade.php` | `HomeController@index` | — |
| `views/products/index.blade.php` | `view('products.index')` | `<livewire:product-list />` |
| `views/products/show.blade.php` | `ProductController@show` | `<livewire:product-reviews />`, `<livewire:favorite-button />` |
| `views/cart/index.blade.php` | `view('cart.index')` | `<livewire:cart-page />` |
| `views/checkout/index.blade.php` | `view('checkout.index')` | `<livewire:checkout />` |
| `views/orders/show.blade.php` | `OrderController@show` | — |
| `views/account/orders.blade.php` | `AccountController@orders` | — |
| `views/account/profile.blade.php` | `AccountController@profile` | `<livewire:account-profile />` |
| `views/account/addresses.blade.php` | `AccountController@addresses` | `<livewire:account-addresses />` |
| `views/account/favorites.blade.php` | `AccountController@favorites` | — |
| `views/static/order-guide.blade.php` | `StaticPageController@orderGuide` | — |
| `views/static/payment.blade.php` | `StaticPageController@payment` | — |
| `views/static/shipping.blade.php` | `StaticPageController@shipping` | — |
| `views/static/returns.blade.php` | `StaticPageController@returns` | — |

### Layout hierarchy

```
layouts/navigation.blade.php   ← main layout, chứa header + <livewire:cart-icon />
  └── components/account-layout.blade.php  ← layout riêng cho /account/* (sidebar + content)

layouts/guest.blade.php         ← dùng cho auth pages (login, register...)
```

### Livewire component → View file

| Class | View |
|---|---|
| `App\Livewire\CartIcon` | `livewire/cart-icon.blade.php` |
| `App\Livewire\Cart` | `livewire/cart.blade.php` |
| `App\Livewire\CartPage` | `livewire/cart-page.blade.php` |
| `App\Livewire\ProductList` | `livewire/product-list.blade.php` |
| `App\Livewire\Checkout` | `livewire/checkout.blade.php` |
| `App\Livewire\ProductReviews` | `livewire/product-reviews.blade.php` |
| `App\Livewire\FavoriteButton` | `livewire/favorite-button.blade.php` |
| `App\Livewire\AccountProfile` | `livewire/account-profile.blade.php` |
| `App\Livewire\AccountAddresses` | `livewire/account-addresses.blade.php` |

---

## 24. AppServiceProvider — những gì đã đăng ký

File: `app/Providers/AppServiceProvider.php`

```php
// boot()
OrderItem::observe(OrderItemObserver::class);  // trừ stock khi OrderItem được tạo
URL::forceScheme('https');                      // chỉ chạy trên production
```

**Không có** service binding, singleton, hay macro nào khác hiện tại.

**Quan trọng:** Nếu thêm Observer mới, phải đăng ký tại đây trong `boot()`. Không dùng `#[ObservedBy]` attribute — dùng `Model::observe()` trong AppServiceProvider để nhất quán.

Filament panel được đăng ký riêng tại `app/Providers/Filament/AdminPanelProvider.php` — xem Section 27.

---

## 25. Checkout sequence — thứ tự thực hiện đầy đủ

Khi user bấm "Đặt hàng" trong `Livewire\Checkout`:

```
1. Livewire\Checkout::placeOrder()
   ├── Validate form (customer_name, phone, address, payment_method...)
   ├── Kiểm tra honeypot + reCAPTCHA + rate limiter (5 lần/phút)
   ├── Nếu có voucher_code: gọi Voucher::isValid() + calculateDiscount()
   └── Gọi PlaceOrderAction::execute($customerData)

2. PlaceOrderAction::execute()  [wrapped trong DB::transaction]
   ├── CartService::getItems() — lấy cart từ session
   ├── Product::whereIn()->lockForUpdate() — verify giá + stock từ DB
   ├── Tính verifiedTotal từ giá DB (bỏ qua giá lưu trong session)
   ├── Order::create() — status = 'pending' (COD) hoặc 'unpaid' (bank_transfer)
   ├── foreach items: $order->items()->create(...)
   │   └── [trigger] OrderItemObserver::created() → Product::decrement('stock')
   ├── Nếu có voucher: Voucher::increment('used_count')
   ├── CartService::clearCart()
   └── session(['last_order_id' => $order->id])  ← dùng để verify ownership

3. Livewire\Checkout redirect → route('orders.show', $order)

4. OrderController::show($order)
   └── Verify: auth()->user()->email === $order->customer_email
       HOẶC session('last_order_id') === $order->id
```

**Điểm dễ vỡ:** Nếu sửa `PlaceOrderAction`, giữ nguyên thứ tự: verify → create Order → create Items (Observer chạy ở đây) → increment voucher → clear cart → set session. Đảo thứ tự sẽ gây ra stock trừ sai hoặc cart không được xóa khi transaction fail.

---

## 26. Cart session — cấu trúc dữ liệu

Session key: `'cart'`

```php
// Cấu trúc: array keyed by product_id (integer)
[
    42 => [
        'product_id' => 42,
        'name'       => 'Figure Naruto Uzumaki – Sage Mode',
        'slug'       => 'figure-naruto-uzumaki-sage-mode',
        'price'      => 850000.0,   // float, lấy từ DB khi addItem()
        'image_url'  => 'products/abc.jpg',  // path hoặc URL đầy đủ
        'quantity'   => 2,          // integer, max = product->stock
    ],
    57 => [ ... ],
]
```

**Lưu ý quan trọng:**
- Key là `product_id` (int), không phải index tuần tự.
- `price` trong session **chỉ dùng để hiển thị** — `PlaceOrderAction` luôn lấy lại giá từ DB khi tạo order.
- `image_url` có thể là path ngắn (`products/abc.jpg`) hoặc URL đầy đủ (legacy) — dùng `$product->image_url` accessor để resolve, không dùng trực tiếp.
- `updateQuantity(id, 0)` hoặc âm → tự động gọi `removeItem()`.

---

## 27. Filament panel config

File: `app/Providers/Filament/AdminPanelProvider.php`

| Thuộc tính | Giá trị |
|---|---|
| Panel ID | `'admin'` |
| Path | `/admin` |
| Auth guard | `'admin'` (guard riêng, không dùng `web`) |
| Theme color | `Color::Amber` (vàng — khác với frontend tím/hồng) |
| Login | Có (`->login()`) |
| Auto-discover Resources | `app/Filament/Resources/` |
| Auto-discover Widgets | `app/Filament/Widgets/` |

**Guard `admin` tách biệt với guard `web`:** Login vào `/admin` không ảnh hưởng session frontend và ngược lại. `User::canAccessPanel()` kiểm tra `$this->isAdmin()` để chặn customer login vào admin.

**Khi tạo Resource/Widget mới:** Đặt đúng namespace `App\Filament\Resources\` hoặc `App\Filament\Widgets\` — panel tự discover, không cần đăng ký thủ công trong `AdminPanelProvider`.

---

## 28. Dual auth guards — `web` vs `admin`

Project có **2 guard hoàn toàn tách biệt**:

| Guard | Dùng cho | Đăng nhập tại |
|---|---|---|
| `web` | Khách hàng (Breeze) | `/login` |
| `admin` | Admin Filament | `/admin/login` |

**Quy tắc khi thêm route mới:**
- Route cần khách hàng đăng nhập → `->middleware('auth')` (guard `web`, mặc định)
- Route thuộc Filament admin → không đặt middleware thủ công, panel tự xử lý
- **KHÔNG** dùng `->middleware('auth:admin')` trên route frontend — khách hàng đã đăng nhập vẫn bị từ chối vì sai guard

```php
// Đúng — route tài khoản khách hàng
Route::middleware('auth')->group(function () {
    Route::get('/account/orders', [AccountController::class, 'orders']);
});

// Sai — nhầm guard
Route::middleware('auth:admin')->get('/account/orders', ...);
```

Session của 2 guard độc lập — login vào `/admin` không ảnh hưởng trạng thái đăng nhập frontend và ngược lại.

---

## 29. `welcome.blade.php` ≠ trang chủ

| File | Vai trò |
|---|---|
| `resources/views/home.blade.php` | Trang chủ thực tế → `HomeController@index` tại route `/` |
| `resources/views/welcome.blade.php` | File mặc định của Laravel, **không dùng**, không được map vào route nào |

Khi cần sửa trang chủ → sửa `home.blade.php`, không phải `welcome.blade.php`.
