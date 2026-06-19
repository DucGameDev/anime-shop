<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Order;
use App\Services\CartService;

class PlaceOrderAction
{
    public function __construct(private readonly CartService $cartService) {}

    public function execute(array $customerData): Order
    {
        $items = $this->cartService->getItems();

        if (empty($items)) {
            throw new \RuntimeException('Giỏ hàng trống');
        }

        $paymentMethod = $customerData['payment_method'] ?? 'bank_transfer';

        $order = Order::create([
            'customer_name'  => $customerData['customer_name'],
            'customer_email' => $customerData['customer_email'] ?? null,
            'phone'          => $customerData['phone'],
            'address'        => $customerData['address'],
            'note'           => $customerData['note'] ?? null,
            'payment_method' => $paymentMethod,
            'status'         => $paymentMethod === 'cod' ? 'pending' : 'unpaid',
            'total_amount'   => $this->cartService->getTotal(),
        ]);

        foreach ($items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        $this->cartService->clearCart();

        return $order;
    }
}
