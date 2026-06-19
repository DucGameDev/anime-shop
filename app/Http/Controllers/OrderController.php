<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Order $order): View
    {
        $order->load('items.product');

        $reviewedProductIds = auth()->check()
            ? Review::where('user_id', auth()->id())->pluck('rating', 'product_id')->toArray()
            : [];

        return view('orders.show', compact('order', 'reviewedProductIds'));
    }
}
