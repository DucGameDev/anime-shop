<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ProductList extends Component
{
    public string $search = '';
    public string $category = '';

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->search = '';
    }

    public function render(): View
    {
        $products = Product::query()
            ->with('category')
            ->when($this->category, fn ($q) => $q->byCategory($this->category))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->inStock()
            ->latest()
            ->get();

        return view('livewire.product-list', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
