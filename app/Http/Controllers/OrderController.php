<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function show(Order $order): View|RedirectResponse
    {
        // Kiểm tra quyền truy cập: chỉ chủ đơn hoặc phiên session có order ID này mới xem được
        $authorised = false;

        if (auth()->check() && $order->customer_email === auth()->user()->email) {
            $authorised = true;
        } elseif (session('last_order_id') === $order->id) {
            $authorised = true;
        }

        if (! $authorised) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $order->load('items.product');

        $reviewedProductIds = auth()->check()
            ? Review::where('user_id', auth()->id())->pluck('rating', 'product_id')->toArray()
            : [];

        return view('orders.show', compact('order', 'reviewedProductIds'));
    }
}
