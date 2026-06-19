<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;
use Livewire\Component;

class ProductReviews extends Component
{
    public Product $product;

    public int    $rating  = 5;
    public string $comment = '';

    public function rules(): array
    {
        return [
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function submit(): void
    {
        if (! auth()->check() || $this->hasReviewed()) {
            return;
        }

        $this->validate();

        $qualifyingOrder = $this->getQualifyingOrder();
        if (! $qualifyingOrder) {
            return;
        }

        Review::create([
            'product_id' => $this->product->id,
            'user_id'    => auth()->id(),
            'order_id'   => $qualifyingOrder->id,
            'rating'     => $this->rating,
            'comment'    => $this->comment ?: null,
        ]);

        $this->dispatch('review-submitted');
    }

    public function canReview(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return $this->getQualifyingOrder() !== null;
    }

    public function hasReviewed(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return Review::where('product_id', $this->product->id)
            ->where('user_id', auth()->id())
            ->exists();
    }

    private function getQualifyingOrder(): ?Order
    {
        return Order::where('customer_email', auth()->user()->email)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $this->product->id))
            ->first();
    }

    public function render(): View
    {
        $reviews = Review::with('user')
            ->where('product_id', $this->product->id)
            ->latest()
            ->get();

        return view('livewire.product-reviews', [
            'reviews'       => $reviews,
            'averageRating' => $this->product->averageRating(),
            'totalReviews'  => $reviews->count(),
            'canReview'     => $this->canReview(),
            'hasReviewed'   => $this->hasReviewed(),
        ]);
    }
}
