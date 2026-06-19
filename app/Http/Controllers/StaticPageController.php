<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function orderGuide(): View
    {
        return view('static.order-guide');
    }

    public function payment(): View
    {
        return view('static.payment');
    }

    public function shipping(): View
    {
        return view('static.shipping');
    }

    public function returns(): View
    {
        return view('static.returns');
    }
}
