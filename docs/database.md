# Database Schema

MySQL database for anime-shop. All monetary values use `decimal(10,2)` or `decimal(12,2)`.

---

## Tables

### `users`
| Column | Type | Constraints |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `name` | varchar(255) | NOT NULL |
| `email` | varchar(255) | NOT NULL, UNIQUE |
| `email_verified_at` | timestamp | nullable |
| `password` | varchar(255) | NOT NULL |
| `remember_token` | varchar(100) | nullable |
| `created_at` | timestamp | nullable |
| `updated_at` | timestamp | nullable |

---

### `categories`
| Column | Type | Constraints |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `name` | varchar(255) | NOT NULL |
| `slug` | varchar(255) | NOT NULL, UNIQUE |
| `created_at` | timestamp | nullable |
| `updated_at` | timestamp | nullable |

Seeded tự động trong migration với 4 bản ghi: `figure`, `ao`, `manga`, `sticker`.

---

### `products`
| Column | Type | Constraints |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `name` | varchar(255) | NOT NULL |
| `slug` | varchar(255) | NOT NULL, UNIQUE |
| `description` | text | NOT NULL |
| `price` | decimal(10,2) | NOT NULL |
| `image_url` | varchar(255) | NOT NULL — path trên disk hoặc URL đầy đủ (legacy) |
| `category_id` | bigint unsigned | NOT NULL, FK → `categories.id` RESTRICT |
| `stock` | int unsigned | NOT NULL, default 0 |
| `created_at` | timestamp | nullable |
| `updated_at` | timestamp | nullable |

> `image_url` được xử lý bởi accessor `getImageUrlAttribute()` trên model: nếu là path → `Storage::disk()->url()`, nếu là URL đầy đủ → trả thẳng.

---

### `orders`
| Column | Type | Constraints |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `customer_name` | varchar(255) | NOT NULL |
| `customer_email` | varchar(255) | nullable — dùng để liên kết guest với `User` |
| `phone` | varchar(20) | NOT NULL |
| `address` | text | NOT NULL |
| `status` | enum | NOT NULL, default `pending` — xem bên dưới |
| `total_amount` | decimal(12,2) | NOT NULL, default 0 |
| `created_at` | timestamp | nullable |
| `updated_at` | timestamp | nullable |

**Giá trị `status`**: `pending` · `shipped` · `completed` · `cancelled`

---

### `order_items`
| Column | Type | Constraints |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `order_id` | bigint unsigned | NOT NULL, FK → `orders.id` CASCADE DELETE |
| `product_id` | bigint unsigned | NOT NULL, FK → `products.id` RESTRICT |
| `quantity` | int unsigned | NOT NULL |
| `price` | decimal(10,2) | NOT NULL — snapshot giá tại thời điểm đặt hàng |
| `created_at` | timestamp | nullable |
| `updated_at` | timestamp | nullable |

---

### `sessions`
| Column | Type | Constraints |
|---|---|---|
| `id` | varchar | PK |
| `user_id` | bigint unsigned | nullable, indexed |
| `ip_address` | varchar(45) | nullable |
| `user_agent` | text | nullable |
| `payload` | longtext | NOT NULL — chứa giỏ hàng (CartService) |
| `last_activity` | int | NOT NULL, indexed |

---

### `password_reset_tokens`
| Column | Type | Constraints |
|---|---|---|
| `email` | varchar(255) | PK |
| `token` | varchar(255) | NOT NULL |
| `created_at` | timestamp | nullable |

---

### `cache` / `cache_locks`
Bảng Laravel cache driver. Không liên quan đến business logic.

| Table | Key columns |
|---|---|
| `cache` | `key` (PK), `value`, `expiration` |
| `cache_locks` | `key` (PK), `owner`, `expiration` |

---

### `jobs` / `job_batches` / `failed_jobs`
Bảng Laravel queue driver. Chưa dùng trong luồng nghiệp vụ hiện tại.

---

## Relations

```
categories ──< products           (categories.id = products.category_id)  RESTRICT delete
orders     ──< order_items        (orders.id = order_items.order_id)       CASCADE delete
products   ──< order_items        (products.id = order_items.product_id)   RESTRICT delete
users      ──< orders             (users.email = orders.customer_email)    soft link (nullable, no FK)
users      ──< sessions           (users.id = sessions.user_id)            nullable
```

### Sơ đồ quan hệ

```
┌─────────────┐        ┌──────────────────┐        ┌──────────────────┐
│  categories │1      *│     products      │*      1│   (order_items)  │
│─────────────│────────│──────────────────│        │                  │
│ id          │        │ id               │        │ product_id ──────┘
│ name        │        │ name             │
│ slug        │        │ slug             │        ┌──────────────────┐
└─────────────┘        │ price            │        │   order_items    │
                       │ image_url        │*      1│──────────────────│
                       │ category_id ─────┘        │ id               │
                       │ stock            │        │ order_id ────────┐
                       └──────────────────┘        │ product_id       │
                                                   │ quantity         │
┌─────────────┐                                    │ price (snapshot) │
│    users    │                                    └──────────────────┘
│─────────────│        ┌──────────────────┐                 │
│ id          │        │      orders      │1               *│
│ name        │        │──────────────────│────────────────-┘
│ email ──────┼──soft──│ customer_email   │
└─────────────┘        │ customer_name    │
                       │ phone            │
                       │ address          │
                       │ status           │
                       │ total_amount     │
                       └──────────────────┘
```

---

## Lịch sử migration

| Batch | File | Nội dung |
|---|---|---|
| 1 | `0001_01_01_000000` | `users`, `password_reset_tokens`, `sessions` |
| 1 | `0001_01_01_000001` | `cache`, `cache_locks` |
| 1 | `0001_01_01_000002` | `jobs`, `job_batches`, `failed_jobs` |
| 2 | `2026_06_15_072953` | `products` (với cột `category` enum — đã refactor) |
| 3 | `2026_06_15_080000` | `orders` |
| 3 | `2026_06_15_080001` | `order_items` |
| 3 | `2026_06_15_090000` | `categories` + seed 4 danh mục |
| 3 | `2026_06_15_090001` | Refactor `products.category` (string) → `products.category_id` (FK) |
| 4 | `2026_06_15_100000` | Thêm `orders.customer_email` |
