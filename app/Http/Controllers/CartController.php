<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function add(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'quantity' => "sometimes|integer|min:1|max:{$product->stock}",
        ]);

        $quantity = (int) $request->input('quantity', 1);
        $this->cartService->addItem($product, $quantity);

        return response()->json([
            'success' => true,
            'count'   => $this->cartService->getItemCount(),
            'message' => 'Đã thêm vào giỏ hàng',
        ]);
    }
}
