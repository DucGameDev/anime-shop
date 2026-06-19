<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function orders(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $orders = $user->orders()
            ->with(['items.product' => fn ($q) => $q->withTrashed()])
            ->latest()
            ->paginate(10);

        $userId = (int) $user->id;

        $reviewedProductIds = Review::where('user_id', $userId)
            ->pluck('rating', 'product_id')
            ->toArray();

        return view('account.orders', compact('orders', 'reviewedProductIds'));
    }

    public function profile(): View
    {
        return view('account.profile');
    }

    public function addresses(): View
    {
        return view('account.addresses');
    }

    public function favorites(): View
    {
        /** @var \App\Models\User $user */
        $user      = auth()->user();
        $favorites = $user->favorites()
            ->with(['category'])
            ->latest('favorites.created_at')
            ->paginate(12);

        return view('account.favorites', compact('favorites'));
    }
}
