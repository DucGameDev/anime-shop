<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'phone',
        'address',
        'note',
        'payment_method',
        'status',
        'total_amount',
        'voucher_code',
        'discount_amount',
    ];

    protected $casts = [
        'total_amount'    => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_email', 'email');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function hasProductReviewedBy(int $productId, int $userId): bool
    {
        return Review::query()
            ->where('order_id', $this->id)
            ->where('product_id', $productId)
            ->where('user_id', $userId)
            ->exists();
    }
}
