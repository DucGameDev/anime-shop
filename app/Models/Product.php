<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image_url',
        'category_id',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating(): float
    {
        $avg = $this->reviews()->avg('rating');

        return $avg !== null ? (float) $avg : 0.0;
    }

    public function scopeByCategory(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('category', fn (Builder $q) => $q->where('slug', $categorySlug));
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function getImageUrlAttribute(): ?string
    {
        $raw = $this->attributes['image_url'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }
        if (str_starts_with($raw, 'http')) {
            return $raw;
        }
        $disk = app()->environment('production') ? 's3' : 'public';
        /** @var \Illuminate\Contracts\Filesystem\Cloud $storage */
        $storage = Storage::disk($disk);
        return $storage->url($raw);
    }
}
