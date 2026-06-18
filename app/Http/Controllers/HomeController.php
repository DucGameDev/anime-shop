<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = Product::inStock()->with('category')->inRandomOrder()->paginate(16);

        return view('home', compact('products'));
    }
}
