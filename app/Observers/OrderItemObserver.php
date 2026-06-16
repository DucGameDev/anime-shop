<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\Product;

class OrderItemObserver
{
    public function created(OrderItem $orderItem): void
    {
        Product::where('id', $orderItem->product_id)
            ->decrement('stock', $orderItem->quantity);
    }
}
