<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;

class PlaceOrderAction
{
    public function __construct(private readonly CartService $cartService) {}

    public function execute(array $customerData): Order
    {
        $items = $this->cartService->getItems();

        if (empty($items)) {
            throw new \RuntimeException('Giỏ hàng trống');
        }

        $paymentMethod  = $customerData['payment_method'] ?? 'bank_transfer';
        $discountAmount = (float) ($customerData['discount_amount'] ?? 0);

        return DB::transaction(function () use ($items, $customerData, $paymentMethod, $discountAmount): Order {
            // Verify giá và stock từ DB — không tin giá lưu trong session
            $productIds  = array_column($items, 'product_id');
            $dbProducts  = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $verifiedTotal = 0.0;
            foreach ($items as $item) {
                $product = $dbProducts->get($item['product_id']);

                if (! $product) {
                    throw new \RuntimeException("Sản phẩm #{$item['product_id']} không còn tồn tại.");
                }

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException("Sản phẩm \"{$product->name}\" chỉ còn {$product->stock} trong kho.");
                }

                $verifiedTotal += (float) $product->price * $item['quantity'];
            }

            $finalTotal = max(0.0, $verifiedTotal - $discountAmount);

            $order = Order::create([
                'customer_name'   => $customerData['customer_name'],
                'customer_email'  => $customerData['customer_email'] ?? null,
                'phone'           => $customerData['phone'],
                'address'         => $customerData['address'],
                'note'            => $customerData['note'] ?? null,
                'payment_method'  => $paymentMethod,
                'status'          => $paymentMethod === 'cod' ? 'pending' : 'unpaid',
                'total_amount'    => $finalTotal,
                'voucher_code'    => $customerData['voucher_code'] ?? null,
                'discount_amount' => $discountAmount,
            ]);

            foreach ($items as $item) {
                $product = $dbProducts->get($item['product_id']);

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price, // dùng giá DB, không dùng giá session
                ]);
            }

            if (! empty($customerData['voucher_code'])) {
                Voucher::where('code', $customerData['voucher_code'])->increment('used_count');
            }

            $this->cartService->clearCart();

            // Lưu vào session để OrderController verify ownership (IDOR prevention)
            session(['last_order_id' => $order->id]);

            return $order;
        });
    }
}
