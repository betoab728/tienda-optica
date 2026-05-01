<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get cart summary
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'cart' => $this->cartService->getCartSummary(),
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = $this->cartService->addItem(
            productId: $request->product_id,
            name: $request->name,
            price: $request->price,
            image: $request->image,
            quantity: $request->quantity ?? 1
        );

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'cart' => $cart,
        ]);
    }

    /**
     * Update item quantity
     */
    public function update(Request $request, int $productId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $this->cartService->updateQuantity($productId, $request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada',
            'cart' => $cart,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(int $productId): JsonResponse
    {
        $cart = $this->cartService->removeItem($productId);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito',
            'cart' => $cart,
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado',
            'cart' => $this->cartService->getCartSummary(),
        ]);
    }

    /**
     * Get cart count only (for header badge)
     */
    public function count(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count' => $this->cartService->getTotalCount(),
        ]);
    }
}
