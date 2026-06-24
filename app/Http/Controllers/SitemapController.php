<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $products   = Product::select(['slug', 'updated_at'])->latest('updated_at')->get();
        $categories = Category::select(['slug', 'updated_at'])->get();

        return response()
            ->view('sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
