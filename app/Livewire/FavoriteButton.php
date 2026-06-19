<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class FavoriteButton extends Component
{
    public int $productId;

    public bool $isFavorited = false;

    public function mount(int $productId): void
    {
        $this->productId   = $productId;
        $this->isFavorited = auth()->check()
            && auth()->user()->favorites()->where('product_id', $productId)->exists();
    }

    public function toggle(): void
    {
        if (! auth()->check()) {
            return; // guest — button is hidden in view
        }

        $user = auth()->user();
        if ($this->isFavorited) {
            $user->favorites()->detach($this->productId);
            $this->isFavorited = false;
        } else {
            $user->favorites()->attach($this->productId);
            $this->isFavorited = true;
        }
    }

    public function render(): View
    {
        return view('livewire.favorite-button');
    }
}
