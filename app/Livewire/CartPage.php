<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\View\View;
use Livewire\Component;

class CartPage extends Component
{
    public function incrementQty(int $productId): void
    {
        $cartService = app(CartService::class);
        $items       = $cartService->getItems();

        if (isset($items[$productId])) {
            $cartService->updateQuantity($productId, $items[$productId]['quantity'] + 1);
        }

        $this->dispatch('cart-updated');
    }

    public function decrementQty(int $productId): void
    {
        $cartService = app(CartService::class);
        $items       = $cartService->getItems();

        if (!isset($items[$productId])) {
            return;
        }

        $newQty = $items[$productId]['quantity'] - 1;

        if ($newQty <= 0) {
            $cartService->removeItem($productId);
        } else {
            $cartService->updateQuantity($productId, $newQty);
        }

        $this->dispatch('cart-updated');
    }

    public function removeItem(int $productId): void
    {
        app(CartService::class)->removeItem($productId);
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        $cartService = app(CartService::class);

        return view('livewire.cart-page', [
            'items'     => $cartService->getItems(),
            'total'     => $cartService->getTotal(),
            'itemCount' => $cartService->getItemCount(),
        ]);
    }
}
