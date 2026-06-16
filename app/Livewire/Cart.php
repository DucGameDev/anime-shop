<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\View\View;
use Livewire\Component;

class Cart extends Component
{
    public array $quantities = [];

    public function mount(): void
    {
        $this->syncQuantities();
    }

    public function removeItem(int $productId): void
    {
        app(CartService::class)->removeItem($productId);
        $this->syncQuantities();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(int $productId): void
    {
        $qty = (int) ($this->quantities[$productId] ?? 1);
        app(CartService::class)->updateQuantity($productId, $qty);
        $this->syncQuantities();
        $this->dispatch('cart-updated');
    }

    public function clearCart(): void
    {
        app(CartService::class)->clearCart();
        $this->quantities = [];
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        $cartService = app(CartService::class);

        return view('livewire.cart', [
            'items' => $cartService->getItems(),
            'total' => $cartService->getTotal(),
        ]);
    }

    private function syncQuantities(): void
    {
        $this->quantities = collect(app(CartService::class)->getItems())
            ->mapWithKeys(fn (array $item) => [(string) $item['product_id'] => $item['quantity']])
            ->all();
    }
}
