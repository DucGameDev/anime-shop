<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public string $search   = '';

    #[Url(as: 'category')]
    public string $category = '';

    public string $sort     = 'newest';
    public int    $seed     = 0;

    public function mount(): void
    {
        $this->seed = rand(1, 99999);
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->search   = '';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Product::query()
            ->with('category')
            ->when($this->category, fn ($q) => $q->byCategory($this->category))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->inStock();

        match ($this->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular'    => $query->orderByDesc(
                                \App\Models\OrderItem::selectRaw('COALESCE(SUM(quantity), 0)')
                                    ->whereColumn('product_id', 'products.id')
                            ),
            'random'     => $query->orderByRaw('RAND(?)', [(int) $this->seed]),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(12);

        return view('livewire.product-list', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
