# Routes

Tất cả route của anime-shop. Filament tự đăng ký route dưới `/admin` qua service provider.

---

## Trang công khai (không cần đăng nhập)

| Method | URI | Action | Tên route | Mô tả |
|---|---|---|---|---|
| GET | `/` | `HomeController@index` | `home` | Trang chủ |
| GET | `/products` | `view('products.index')` | `products.index` | Danh sách sản phẩm — Livewire `ProductList` (search + filter category) |
| GET | `/products/{product:slug}` | `ProductController@show` | `products.show` | Chi tiết sản phẩm — route model binding theo `slug` |
| GET | `/orders/{order}` | `OrderController@show` | `orders.show` | Trang xác nhận đơn hàng sau khi đặt |

---

## Giỏ hàng

| Method | URI | Action | Tên route | Mô tả |
|---|---|---|---|---|
| GET | `/cart` | `view('cart.index')` | `cart.index` | Trang giỏ hàng — Livewire `CartPage` (tăng/giảm qty, xóa) |
| POST | `/cart/{product}` | `CartController@add` | `cart.add` | Thêm sản phẩm vào giỏ (lưu session qua `CartService`) |

---

## Checkout

| Method | URI | Action | Tên route | Mô tả |
|---|---|---|---|---|
| GET | `/checkout` | `view('checkout.index')` | `checkout.index` | Trang checkout — Livewire `Checkout` (form + validation + `PlaceOrderAction`) |

---

## Auth — Breeze (guest)

Route yêu cầu middleware `guest` (chuyển hướng nếu đã đăng nhập).

| Method | URI | Action | Tên route | Mô tả |
|---|---|---|---|---|
| GET | `/register` | `RegisteredUserController@create` | `register` | Form đăng ký |
| POST | `/register` | `RegisteredUserController@store` | — | Xử lý đăng ký |
| GET | `/login` | `AuthenticatedSessionController@create` | `login` | Form đăng nhập |
| POST | `/login` | `AuthenticatedSessionController@store` | — | Xử lý đăng nhập |
| GET | `/forgot-password` | `PasswordResetLinkController@create` | `password.request` | Form quên mật khẩu |
| POST | `/forgot-password` | `PasswordResetLinkController@store` | `password.email` | Gửi link reset |
| GET | `/reset-password/{token}` | `NewPasswordController@create` | `password.reset` | Form đặt lại mật khẩu |
| POST | `/reset-password` | `NewPasswordController@store` | `password.store` | Xử lý đặt lại mật khẩu |

---

## Auth — Breeze (đã đăng nhập)

Route yêu cầu middleware `auth`.

| Method | URI | Action | Tên route | Mô tả |
|---|---|---|---|---|
| GET | `/dashboard` | `view('dashboard')` | `dashboard` | Dashboard người dùng (middleware `auth`, `verified`) |
| GET | `/profile` | `ProfileController@edit` | `profile.edit` | Form chỉnh sửa hồ sơ |
| PATCH | `/profile` | `ProfileController@update` | `profile.update` | Lưu thay đổi hồ sơ |
| DELETE | `/profile` | `ProfileController@destroy` | `profile.destroy` | Xóa tài khoản |
| GET | `/verify-email` | `EmailVerificationPromptController` | `verification.notice` | Thông báo chờ xác minh email |
| GET | `/verify-email/{id}/{hash}` | `VerifyEmailController` | `verification.verify` | Xác minh email (signed + throttle 6/min) |
| POST | `/email/verification-notification` | `EmailVerificationNotificationController@store` | `verification.send` | Gửi lại email xác minh (throttle 6/min) |
| GET | `/confirm-password` | `ConfirmablePasswordController@show` | `password.confirm` | Xác nhận lại mật khẩu |
| POST | `/confirm-password` | `ConfirmablePasswordController@store` | — | Xử lý xác nhận mật khẩu |
| PUT | `/password` | `PasswordController@update` | `password.update` | Đổi mật khẩu |
| POST | `/logout` | `AuthenticatedSessionController@destroy` | `logout` | Đăng xuất |

---

## Admin — Filament (`/admin`)

Route được Filament tự đăng ký qua `FilamentServiceProvider`. Yêu cầu user implement `FilamentUser` và `canAccessPanel()` trả về `true`.

| URI | Mô tả |
|---|---|
| `/admin` | Dashboard — `StatsOverviewWidget`, `RevenueChartWidget`, `LatestOrdersWidget` |
| `/admin/products` | Danh sách sản phẩm — search, filter category |
| `/admin/products/create` | Tạo sản phẩm mới — FileUpload ảnh (disk theo `APP_ENV`) |
| `/admin/products/{id}/edit` | Sửa sản phẩm |
| `/admin/categories` | Danh sách danh mục — guard xóa nếu còn sản phẩm |
| `/admin/categories/create` | Tạo danh mục mới |
| `/admin/categories/{id}/edit` | Sửa danh mục |
| `/admin/orders` | Danh sách đơn hàng — badge trạng thái, filter status |
| `/admin/orders/{id}/edit` | Sửa trạng thái đơn hàng (chỉ field `status`) |
| `/admin/users` | Danh sách người dùng — chỉ xem, thống kê đơn + doanh thu |
| `/admin/users/{id}` | Chi tiết người dùng — `OrdersRelationManager` (read-only) |
| `/admin/login` | Trang đăng nhập Filament |

> Filament không cho phép `canCreate` / `canEdit` / `canDelete` trên `UserResource`. `OrderResource` tắt `canCreate`.

---

## Tóm tắt middleware

| Middleware | Áp dụng cho |
|---|---|
| *(không có)* | Trang công khai, giỏ hàng, checkout, orders.show |
| `guest` | Các route auth (register, login, forgot/reset password) |
| `auth` | Dashboard, profile, password, verify-email, logout |
| `auth` + `verified` | `/dashboard` |
| `signed` + `throttle:6,1` | `/verify-email/{id}/{hash}` |
| Filament panel guard | `/admin/**` |
