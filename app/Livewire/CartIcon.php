<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\View\View;
use Livewire\Component;

class CartIcon extends Component
{
    public function render(): View
    {
        return view('livewire.cart-icon', [
            'count' => app(CartService::class)->getItemCount(),
        ]);
    }
}
