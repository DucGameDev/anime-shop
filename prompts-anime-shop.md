# Bộ prompt cho Claude Code — Website bán đồ anime
## Laravel + Livewire + Filament + Docker + MySQL

Chạy tuần tự từng prompt, test sau mỗi bước (mở `localhost`, kiểm tra ở 375px / 768px / 1280px). Phần **"Giao diện"** mô tả UI mong đợi sau khi chạy prompt — dùng để đối chiếu kết quả.

---

## Prompt 1 — Khởi tạo project + Docker (local dev)

```
Tạo một project Laravel mới tên "anime-shop" trong thư mục hiện tại, dùng
Laravel mới nhất, kèm Breeze cho authentication.

Sau đó tạo cấu hình Docker cho môi trường local development:
- Dockerfile cho PHP-FPM (PHP 8.2+)
- docker-compose.yml với các service: app (PHP-FPM), nginx, mysql 8,
  phpmyadmin
- File nginx config phù hợp cho Laravel (point đến public/index.php)
- Volume để code thay đổi local được phản ánh ngay trong container

Cập nhật .env để dùng MySQL, DB_HOST trỏ tới service mysql trong
docker-compose.

Hướng dẫn tôi lệnh build và chạy: docker compose up, sau đó migrate
trong container.
```

**Giao diện**: chưa có UI riêng — chỉ kiểm tra `localhost` trả về trang welcome mặc định của Laravel, phpMyAdmin truy cập được ở port riêng (thường 8080 hoặc do bạn cấu hình).

---

## Prompt 2 — Database: Migration và Model sản phẩm

```
Tạo migration và Model cho "Product" với các trường:
- name (string)
- slug (string, unique)
- description (text)
- price (decimal)
- image_url (string)
- category (string) — figure, ao, manga, sticker
- stock (integer)
- timestamps

Tạo Seeder ProductSeeder tạo 12 sản phẩm mẫu cho shop bán đồ anime,
ảnh dùng placeholder từ https://placehold.co.

Chạy migrate và seed trong container Docker, kiểm tra dữ liệu.
```

**Giao diện**: chưa có UI — kiểm tra qua phpMyAdmin xem bảng `products` có 12 dòng dữ liệu.

---

## Prompt 3 — Cài Livewire + Layout chung + Design tokens

```
Cài Livewire và Alpine.js vào project.

Cấu hình tailwind.config.js với color tokens:
- primary: light #F3E8FF, DEFAULT #A855F7, dark #7E22CE (tone tím)
- secondary: light #FCE7F3, DEFAULT #EC4899 (tone hồng)
- neutral: text #374151, muted #9CA3AF, bg #F9FAFB

Tạo các Blade component dùng chung trong resources/views/components/:
- <x-button> với prop variant (primary/secondary) và size (sm/base)
- <x-input> cho form input/select, focus state dùng color tokens
- <x-container> bọc max-w-7xl mx-auto px-4 sm:px-6 lg:px-8

Tạo layout chung resources/views/layouts/app.blade.php dùng Tailwind:
- Header: logo "AnimeShop" (text-primary-dark), menu ngang
  (Trang chủ, Sản phẩm, Giỏ hàng) ẩn trên mobile sau hamburger menu
  (toggle bằng Alpine.js), icon giỏ hàng có badge số lượng
- Footer: thông tin liên hệ và copyright, nền neutral-bg

Đảm bảo layout dùng @livewireStyles và @livewireScripts.
```

**Giao diện**:
- **Mobile (< 768px)**: header chỉ hiện logo bên trái, icon giỏ hàng + hamburger bên phải. Bấm hamburger mở menu dọc (dropdown) chứa Trang chủ/Sản phẩm/Giỏ hàng.
- **Desktop (≥ 1024px)**: header hiện đầy đủ logo trái — menu ngang giữa — icon giỏ hàng phải, không có hamburger.
- Màu chủ đạo: nền trắng, accent tím (`primary`) cho logo/link active, hồng (`secondary`) cho badge/hover.
- Footer nền `neutral-bg`, text `neutral-text`, căn giữa trên mobile, chia cột trên desktop.

---

## Prompt 4 — Trang chủ + ProductCard

```
Tạo Blade component <x-product-card :product="$product" />:
- Ảnh dùng aspect-square object-cover, rounded-lg
- Badge category (góc trên ảnh hoặc dưới ảnh) màu theo category
  (ví dụ figure=primary-light, ao=secondary-light...)
- Tên sản phẩm (line-clamp-2), giá format VNĐ (text-primary-dark font-bold)
- <x-button variant="primary" size="sm"> "Thêm vào giỏ"

Tạo route GET / và HomeController@index lấy tất cả sản phẩm từ database.

Tạo view resources/views/home.blade.php kế thừa layout app:
- Banner trên cùng: nền primary-light, tiêu đề lớn + nút CTA
  (text-2xl md:text-4xl cho heading, py-8 lg:py-16)
- Section "Sản phẩm nổi bật": lưới grid-cols-2 md:grid-cols-3
  lg:grid-cols-4 gap-4 lg:gap-6, dùng x-product-card
```

**Giao diện**:
- **Banner**: full width, nền tím nhạt (`primary-light`), tiêu đề + mô tả ngắn căn giữa, nút "Mua ngay" nền tím đậm chữ trắng. Chiều cao co lại trên mobile, cao hơn trên desktop.
- **Lưới sản phẩm**: 2 cột trên mobile, 3 cột tablet, 4 cột desktop. Mỗi card: ảnh vuông, badge category nhỏ góc trên-trái, tên sản phẩm tối đa 2 dòng, giá nổi bật màu tím, nút "Thêm vào giỏ" full width đáy card.
- Card có border mỏng, rounded-lg, hover nhẹ (shadow hoặc border đậm hơn).

---

## Prompt 5 — Trang sản phẩm với filter Livewire (realtime)

```
Tạo Livewire component "ProductList" (app/Livewire/ProductList.php
và view tương ứng):
- Thanh filter category dạng pill/tab ngang, scroll ngang trên mobile
  (overflow-x-auto), các nút: Tất cả, Figure, Áo, Manga, Sticker —
  nút active có nền primary, chữ trắng; nút inactive nền trắng border
- Ô tìm kiếm theo tên sản phẩm (wire:model.live), icon search bên trong
  input, full width trên mobile
- Lưới sản phẩm dùng x-product-card, cập nhật realtime theo filter/search
- Khi không có kết quả: hiển thị illustration/icon đơn giản + text
  "Không tìm thấy sản phẩm phù hợp"

Tạo route GET /products trỏ tới view bọc <livewire:product-list />
```

**Giao diện**:
- Trên cùng: ô search full width (mobile) hoặc max-w-md (desktop), icon kính lúp bên trái input.
- Dưới search: dãy pill filter category, scroll ngang trên mobile nếu không đủ chỗ, hàng ngang cố định trên desktop.
- Lưới sản phẩm bên dưới giống trang chủ (2/3/4 cột), cập nhật mượt (Livewire loading state nhẹ — có thể thêm `wire:loading` mờ lưới khi đang fetch).
- Trạng thái rỗng: căn giữa, icon lớn xám nhạt, text `neutral-muted`.

---

## Prompt 6 — Trang chi tiết sản phẩm

```
Thêm route GET /products/{product:slug}, dùng route model binding theo slug.

Tạo view resources/views/products/show.blade.php:
- Breadcrumb nhỏ phía trên (Trang chủ / Sản phẩm / [Tên sản phẩm])
- Layout: flex-col trên mobile, lg:flex-row trên desktop
  - Ảnh sản phẩm: w-full lg:w-1/2, aspect-square object-cover, rounded-lg
  - Thông tin: w-full lg:w-1/2, padding-left trên desktop
    - Badge category, tên sản phẩm (text-xl lg:text-3xl font-bold)
    - Giá (text-2xl text-primary-dark font-bold)
    - Mô tả (text-sm md:text-base text-neutral-text)
    - Tồn kho (text-sm text-neutral-muted)
    - Bộ chọn số lượng (nút -/+ và input number, dùng x-input)
    - <x-button variant="primary"> "Thêm vào giỏ" — full width mobile,
      w-auto desktop

Nếu không tìm thấy sản phẩm, Laravel tự trả 404, tạo view 404 theo
style chung (căn giữa, icon, nút quay về trang chủ)
```

**Giao diện**:
- **Mobile**: ảnh full width trên cùng, thông tin xếp dọc dưới ảnh, nút "Thêm vào giỏ" full width ở cuối.
- **Desktop**: ảnh bên trái 50%, thông tin bên phải 50%, nút "Thêm vào giỏ" không cần full width.
- Bộ chọn số lượng: 3 ô ngang (nút trừ — số — nút cộng), border nhẹ, rounded-md, cùng style với `<x-input>`.
- Trang 404: icon lớn (ví dụ hộp/figure rỗng), text "Không tìm thấy sản phẩm", nút quay về `/products`.

---

## Prompt 7 — Giỏ hàng bằng Livewire (session-based)

```
Tạo CartService (app/Services/CartService.php) quản lý giỏ hàng trong
session: addItem, removeItem, updateQuantity, clearCart, getTotal,
getItemCount.

Tạo Livewire component "Cart" (app/Livewire/Cart.php) gọi CartService
cho các action trên.

Tạo Livewire component "CartIcon" (app/Livewire/CartIcon.php):
- Hiển thị icon giỏ hàng + badge số lượng (nền secondary, chữ trắng,
  rounded-full, size nhỏ, vị trí absolute góc trên-phải icon)
- Tự cập nhật realtime khi có event "cart-updated" được dispatch

Cập nhật nút "Thêm vào giỏ" ở ProductCard và trang chi tiết sản phẩm
để gọi addItem và dispatch event "cart-updated". Khi bấm, hiển thị
toast nhỏ góc dưới màn hình "Đã thêm vào giỏ hàng" (Alpine.js,
tự ẩn sau ~2s).
```

**Giao diện**:
- Icon giỏ hàng ở header: badge tròn màu hồng (`secondary`) hiện số lượng, cập nhật ngay không cần reload khi thêm sản phẩm.
- Toast xác nhận: nền tối nhẹ hoặc trắng có border, góc dưới-phải (desktop) hoặc dưới cùng full width (mobile), tự biến mất.

---

## Prompt 8 — Trang giỏ hàng

```
Tạo Livewire component "CartPage" và route GET /cart bọc
<livewire:cart-page />:

- Layout: flex-col trên mobile, lg:flex-row trên desktop
  - Danh sách sản phẩm (lg:w-2/3): mỗi item là 1 row gồm ảnh nhỏ
    (w-16 h-16 rounded-md object-cover), tên + giá, bộ chọn số lượng
    (nút -/+ wire:click), nút xóa (icon thùng rác, màu đỏ nhạt)
  - Tóm tắt đơn hàng (lg:w-1/3): card nền trắng border, hiển thị
    tổng số lượng, tổng tiền (text-xl font-bold text-primary-dark),
    <x-button variant="primary"> "Tiến hành thanh toán" full width,
    link tới /checkout

- Nếu giỏ rỗng: căn giữa, icon giỏ hàng rỗng lớn, text
  "Giỏ hàng của bạn đang trống", <x-button> link quay lại /products
```

**Giao diện**:
- **Mobile**: danh sách sản phẩm xếp dọc, mỗi item là 1 dòng ngang (ảnh trái, info giữa, nút xóa phải). Tóm tắt đơn hàng nằm dưới danh sách, full width, dạng card nổi (sticky bottom nếu muốn).
- **Desktop**: 2 cột — danh sách 2/3 trái, tóm tắt đơn hàng 1/3 phải (có thể sticky khi scroll).
- Trạng thái rỗng: căn giữa toàn màn hình, icon lớn xám, nút CTA tím dẫn về `/products`.

---

## Prompt 9 — Cài Filament + trang admin quản lý sản phẩm

```
Cài đặt Filament admin panel vào project (filament/filament).

Sau khi cài:
- Hướng dẫn tôi lệnh tạo Filament User admin (chạy trong container,
  tôi tự điền email/password thật)
- Tạo Filament Resource cho Product (make:filament-resource Product)
  với fields: name, slug (auto từ name), description (rich editor),
  price, image_url (file upload, lưu vào disk "public"), category
  (select: figure/ao/manga/sticker), stock

Bảng danh sách Resource có sort, search theo name, filter theo category.
Cho tôi biết URL truy cập trang admin.
```

**Giao diện**:
- Trang admin tại `/admin`, theme mặc định của Filament (sidebar trái chứa menu "Products", nền trắng/xám nhạt, phong cách hiện đại — khác với theme tím-hồng của frontend, không cần đồng bộ màu).
- Trang danh sách Product: bảng có cột ảnh thumbnail, tên, category (badge), giá, stock; ô search trên cùng, filter category dạng dropdown.
- Form thêm/sửa: chia layout 2 cột trên desktop (ảnh upload bên trái, các field text/select bên phải), 1 cột trên mobile.

---

## Prompt 10 — Checkout và Order

```
Tạo migration + Model Order, OrderItem (order_id, product_id, quantity,
price tại thời điểm mua, order có: customer_name, phone, address,
status mặc định "pending").

Tạo Action PlaceOrderAction: tạo Order từ giỏ hàng session, lưu
OrderItems, trừ stock sản phẩm (qua Observer hoặc event), xóa giỏ
hàng sau khi thành công.

Tạo route GET/POST /checkout và Livewire component "Checkout":
- Layout: flex-col trên mobile, lg:flex-row trên desktop
  - Form (lg:w-2/3): input tên, số điện thoại, địa chỉ (dùng <x-input>),
    validate qua Form Request hoặc Livewire rules, hiển thị lỗi dưới
    từng input (text-red-600 text-sm)
  - Tóm tắt đơn hàng (lg:w-1/3): liệt kê sản phẩm rút gọn (tên x
    số lượng — giá), tổng tiền, <x-button variant="primary">
    "Đặt hàng" full width

Sau khi đặt hàng thành công, chuyển tới /orders/{id}:
- Card căn giữa: icon check tròn (nền secondary-light, icon màu
  secondary), "Đặt hàng thành công", mã đơn hàng, trạng thái
  "Đang xử lý" (badge màu vàng/amber), tóm tắt đơn hàng,
  <x-button> "Tiếp tục mua sắm"

Thêm Filament Resource cho Order: bảng danh sách có cột mã đơn,
khách hàng, tổng tiền, trạng thái (badge màu theo status), form sửa
có select đổi trạng thái (pending/shipped/completed/cancelled).
```

**Giao diện**:
- **Trang checkout**: giống bố cục trang giỏ hàng (form trái 2/3, tóm tắt phải 1/3 trên desktop; xếp dọc trên mobile). Input full width, label phía trên input, lỗi validate hiện ngay dưới input màu đỏ.
- **Trang xác nhận đơn hàng**: card trắng căn giữa max-w-md, icon thành công lớn ở trên, thông tin đơn hàng dạng list key-value, badge trạng thái màu amber ("Đang xử lý").
- **Admin Order**: bảng Filament có badge màu cho từng status (pending = amber, shipped = blue, completed = green, cancelled = red/gray).

---

## Prompt 10b — Dashboard tổng quan (Filament)

```
Tạo Filament Dashboard với các widget thống kê cho admin:
- StatsOverviewWidget: 4 thẻ số liệu — Tổng đơn hàng hôm nay,
  Doanh thu hôm nay, Đơn hàng đang xử lý (status=pending), Sản phẩm
  sắp hết hàng (stock < 5)
- ChartWidget (line/bar): doanh thu 7 ngày gần nhất, lấy từ bảng orders
- TableWidget: 5 đơn hàng mới nhất, gồm mã đơn, khách hàng, tổng tiền,
  trạng thái (badge màu)

Đặt các widget này làm trang mặc định khi đăng nhập /admin.
```

**Giao diện**:
- 4 thẻ thống kê dạng lưới: `grid-cols-2` trên mobile, `grid-cols-4` trên desktop, mỗi thẻ có label nhỏ phía trên và số lớn phía dưới (theo style metric card chuẩn).
- Biểu đồ doanh thu 7 ngày nằm dưới, full width trên mobile, chiếm khoảng 2/3 chiều ngang trên desktop nếu đặt cạnh bảng đơn hàng mới nhất.
- Bảng "Đơn hàng mới nhất": cuộn ngang trên mobile nếu cần, badge trạng thái màu theo status giống Order Resource.

---

## Prompt 10c — Quản lý danh mục (Category) thay cho chuỗi cố định

```
Tạo migration + Model Category (id, name, slug), seed 4 danh mục:
Figure, Áo, Manga, Sticker.

Cập nhật migration/Model Product: đổi cột category (string) thành
category_id (foreign key tới categories), cập nhật ProductSeeder
và mọi nơi đang query theo category string (ProductList, scope
category, ProductCard badge) để dùng quan hệ Category.

Tạo Filament Resource cho Category: bảng danh sách (tên, slug, số
sản phẩm thuộc danh mục), form thêm/sửa chỉ có field "name"
(slug tự generate). Cho phép xóa danh mục chỉ khi không còn sản
phẩm nào thuộc danh mục đó (validate trước khi xóa).
```

**Giao diện**:
- Trang admin Category: bảng đơn giản 3 cột (Tên, Slug, Số sản phẩm), nút "Tạo mới" góc trên-phải, form thêm/sửa chỉ 1 ô input.
- Phía frontend: filter pill ở `/products` và badge category trên ProductCard tự động hiển thị theo tên Category từ database, không thay đổi giao diện đã có — chỉ đổi nguồn dữ liệu.

---

## Prompt 10d — Quản lý khách hàng (Customer)

```
Tạo Filament Resource cho User (khách hàng đã đặt hàng):
- Bảng danh sách: tên, email, số đơn hàng đã đặt, tổng tiền đã chi
  (tính từ quan hệ orders), ngày tham gia
- Trang chi tiết (view, không cho sửa trực tiếp): thông tin user +
  bảng lịch sử đơn hàng của user đó (mã đơn, ngày, tổng tiền, trạng thái)
- Không cho admin tạo/sửa/xóa user qua trang này (chỉ xem) — vô hiệu
  hóa action create/edit/delete trong Resource

Nếu Order hiện tại chưa liên kết với User (checkout dạng guest), thêm
cột customer_email vào bảng orders và group theo email khi tính số
đơn/tổng chi tiêu thay vì user_id.
```

**Giao diện**:
- Trang admin Customer: bảng danh sách giống các Resource khác (tên/email, số đơn, tổng chi tiêu — định dạng VNĐ), không có nút "Tạo mới" hay cột action sửa/xóa.
- Trang chi tiết: phần thông tin khách hàng trên cùng (card), bảng lịch sử đơn hàng bên dưới — mỗi dòng có thể click để xem chi tiết Order tương ứng.

---

## Prompt 11 — Production Dockerfile

```
Tạo Dockerfile.prod cho project Laravel (đã có Livewire + Filament),
multi-stage build:
- Stage 1: build assets (Node, npm run build cho Vite/Tailwind)
- Stage 2: PHP-FPM với extensions cần thiết (pdo_mysql, mbstring,
  exif, pcntl, bcmath, gd...), copy code và assets đã build, set
  quyền đúng cho storage và bootstrap/cache, chạy
  composer install --no-dev --optimize-autoloader

Tạo docker-entrypoint.sh chạy khi container start:
php artisan migrate --force, storage:link, config:cache, route:cache,
view:cache.

Thêm HEALTHCHECK kiểm tra app còn sống không.
```

**Giao diện**: không có UI — bước hạ tầng.

---

## Prompt 12 — Storage ảnh production (S3-compatible)

```
Cài package league/flysystem-aws-s3-v3. Cấu hình config/filesystems.php
thêm disk "s3" đọc từ biến môi trường (AWS_*  hoặc tương đương cho
DigitalOcean Spaces/Cloudflare R2).

Cập nhật Filament Resource Product để upload ảnh lưu vào disk "s3"
khi APP_ENV=production, vẫn dùng disk "public" khi local.

Cập nhật .env.example thêm các biến AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY,
AWS_DEFAULT_REGION, AWS_BUCKET, AWS_ENDPOINT, AWS_USE_PATH_STYLE_ENDPOINT.
```

**Giao diện**: không thay đổi UI — chỉ ảnh hưởng nơi lưu file ảnh.

---

## Prompt 13 — Biến môi trường an toàn cho production

```
Tạo file .env.example đầy đủ biến cần thiết (DB_*, APP_KEY,
APP_ENV=production, APP_DEBUG=false, FILAMENT_*, MAIL_*, AWS_*...),
không chứa giá trị thật.

Kiểm tra .gitignore đảm bảo .env, .env.production không bị commit.

Giải thích cách generate APP_KEY mới cho production và rủi ro nếu
APP_DEBUG=true bị để nhầm trên production.
```

---

## Prompt 14 — File context cho Claude Code (CLAUDE.md)

```
Tạo file CLAUDE.md ở root project, gồm các section sau:

## 1. Tổng quan
Website bán đồ anime (figure, áo, manga, sticker), Laravel + Livewire
(frontend khách) + Filament (trang admin), Tailwind CSS, MySQL,
chạy trong Docker.

## 2. Cấu trúc thư mục
app/Livewire, app/Filament/Resources, app/Services, app/Actions,
app/Observers, app/Contracts, resources/views/components.

## 3. Stack
PHP version, Laravel version, Livewire version, Filament version,
MySQL, Docker (lấy version thật từ composer.json).

## 4. Coding Standards
- PSR-12, PascalCase cho class, camelCase cho method/variable,
  snake_case số nhiều cho table, kebab-case.dot cho route name
- Type hints và return types cho mọi method (PHP 8 strict typing)
- Controller chỉ điều hướng, business logic nằm trong Service/Action
- Validate qua Form Request hoặc Livewire validation rules,
  không viết rules trực tiếp trong controller
- Eloquent + Model scope cho query tái sử dụng, không raw SQL
  trừ khi có lý do rõ ràng
- Migration luôn có down(), Model dùng $fillable rõ ràng
- Log lỗi quan trọng bằng Log::error() với context, không log
  thông tin nhạy cảm
- Commit message: feat:/fix:/refactor:/docs:, mỗi commit là
  1 thay đổi logic hoàn chỉnh

## 5. Design Patterns
- Business logic phức tạp/tái sử dụng → Action classes (app/Actions/),
  method execute() hoặc handle()
- Model events lặp lại (trừ stock, gửi notification) → Observer
  pattern (app/Observers/)
- Thanh toán: interface PaymentGateway (app/Contracts/), mỗi cổng
  implement riêng, không if/else theo tên cổng trong controller
- KHÔNG dùng Repository pattern — dùng Eloquent scope cho query
  tái sử dụng. Nếu thấy cần Repository, dừng lại và hỏi tôi trước
  khi implement
- Ưu tiên pattern đơn giản nhất giải quyết được vấn đề, không thêm
  abstraction "phòng khi cần sau này"

## 6. CSS & Responsive Conventions
- Mobile-first, breakpoint Tailwind chuẩn (sm/md/lg/xl), không tạo
  breakpoint custom
- Màu sắc: chỉ dùng color tokens trong tailwind.config.js (primary,
  secondary, neutral), KHÔNG dùng màu Tailwind mặc định trực tiếp
  trong view
- Component UI dùng chung (x-button, x-input, x-container) đặt trong
  resources/views/components/, mọi page phải tái sử dụng
- Product grid luôn: grid-cols-2 md:grid-cols-3 lg:grid-cols-4,
  gap-4 lg:gap-6
- Ảnh sản phẩm luôn aspect-square + object-cover
- Component/page mới có UI PHẢI kiểm tra responsive ở 375px, 768px,
  1280px trước khi coi là hoàn thành

## 7. Lệnh thường dùng (trong Docker)
docker compose exec app php artisan migrate
docker compose exec app php artisan make:livewire ComponentName
docker compose exec app php artisan make:filament-resource Name
docker compose exec app composer install
docker compose exec app npm run dev

## 8. Trạng thái hiện tại
Liệt kê tính năng đã hoàn thành / đang làm / dự kiến, các quyết định
kỹ thuật đã chọn (session cho giỏ hàng, MySQL, S3 cho ảnh production...).

## 9. Bảo mật
Không commit .env, không hardcode key, không log thông tin nhạy cảm.

Quét toàn bộ project hiện tại để file CLAUDE.md phản ánh đúng cấu
trúc thực tế, không phải giả định.
```

---

## Prompt 15 — Tài liệu database và routes

```
Tạo file docs/database.md mô tả schema từ các migration hiện có
(tables, columns, types, relations giữa products/orders/order_items).

Tạo file docs/routes.md liệt kê tất cả route (method, URI, action,
tên route, mô tả), nhóm theo: trang công khai, giỏ hàng, checkout,
admin (Filament).
```

---

## Prompt 16 — Code quality tools

```
Cài Laravel Pint và Larastan (PHPStan cho Laravel) vào project, tạo
config cơ bản, hướng dẫn tôi chạy chúng trước mỗi commit (trong Docker).
```

---

## Prompt 17 — Git commit

```
Hãy git init (nếu chưa có), kiểm tra .gitignore chuẩn cho Laravel +
Filament + Docker (bỏ qua vendor, node_modules, .env,
bootstrap/cache/*.php không cần thiết...), add tất cả và commit
với message "feat: initial setup - Livewire frontend, Filament admin,
Docker, checkout flow"
```

---

## Tổng quan luồng giao diện (sau khi hoàn thành tất cả prompt)

| Trang | Mobile | Desktop |
|---|---|---|
| Trang chủ (`/`) | Banner tím nhạt + lưới sản phẩm 2 cột | Banner lớn hơn + lưới 4 cột |
| Sản phẩm (`/products`) | Search + filter pill scroll ngang + lưới 2 cột | Search + filter hàng ngang + lưới 4 cột |
| Chi tiết (`/products/{slug}`) | Ảnh trên, info dưới, nút full width | Ảnh trái 50%, info phải 50% |
| Giỏ hàng (`/cart`) | List dọc + tóm tắt dưới | List 2/3 trái + tóm tắt 1/3 phải |
| Checkout (`/checkout`) | Form trên, tóm tắt dưới | Form 2/3 trái, tóm tắt 1/3 phải |
| Đơn hàng (`/orders/{id}`) | Card xác nhận căn giữa, full width | Card căn giữa, max-w-md |
| Admin Dashboard (`/admin`) | 4 thẻ thống kê 2 cột, chart full width | 4 thẻ thống kê 1 hàng, chart + bảng đơn mới song song |
| Admin Sản phẩm/Đơn hàng/Danh mục (`/admin/*`) | Filament responsive mặc định, bảng cuộn ngang | Sidebar trái + bảng/form 2 cột |
| Admin Khách hàng (`/admin/users`) | Bảng cuộn ngang, chỉ xem | Bảng đầy đủ + trang chi tiết có lịch sử đơn hàng |

Màu chủ đạo xuyên suốt: nền trắng, accent **tím** (primary) cho thương hiệu/nút chính, **hồng** (secondary) cho badge/nhấn phụ, **vàng/amber** cho trạng thái "đang xử lý", **xanh/đỏ** cho trạng thái thành công/hủy ở phần admin.
