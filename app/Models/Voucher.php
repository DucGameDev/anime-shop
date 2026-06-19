<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value'      => 'decimal:2',
        'min_order'  => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->min_order !== null && $orderTotal < (float) $this->min_order) {
            return 0.0;
        }
        if ($this->type === 'percent') {
            return round($orderTotal * (float) $this->value / 100, 2);
        }

        return min((float) $this->value, $orderTotal);
    }
}
