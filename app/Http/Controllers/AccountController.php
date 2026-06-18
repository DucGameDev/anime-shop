<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class AccountController extends Controller
{
    public function orders(): View
    {
        $orders = auth()->user()
            ->orders()
            ->with(['items.product' => fn ($q) => $q->withTrashed()])
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }
}
