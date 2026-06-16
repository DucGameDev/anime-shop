<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function addItem(Product $product, int $quantity = 1): void
    {
        $cart = $this->getCart();
        $id   = $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = min(
                $cart[$id]['quantity'] + $quantity,
                $product->stock
            );
        } else {
            $cart[$id] = [
                'product_id' => $id,
                'name'       => $product->name,
                'slug'       => $product->slug,
                'price'      => (float) $product->price,
                'image_url'  => $product->image_url,
                'quantity'   => min($quantity, $product->stock),
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function removeItem(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $cart = $this->getCart();
        if (!isset($cart[$productId])) {
            return;
        }

        $cart[$productId]['quantity'] = $quantity;
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clearCart(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function getItems(): array
    {
        return $this->getCart();
    }

    public function getTotal(): float
    {
        return (float) collect($this->getCart())
            ->sum(fn (array $item) => $item['price'] * $item['quantity']);
    }

    public function getItemCount(): int
    {
        return (int) collect($this->getCart())
            ->sum(fn (array $item) => $item['quantity']);
    }

    private function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
