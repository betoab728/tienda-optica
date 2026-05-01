<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    private const CART_SESSION_KEY = 'shopping_cart';

    /**
     * Get all cart items
     */
    public function getCart(): array
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    /**
     * Add item to cart or update quantity if exists
     */
    public function addItem(int $productId, string $name, float $price, string $image, int $quantity = 1): array
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            // Update quantity if item exists
            $cart[$productId]['quantity'] += $quantity;
        } else {
            // Add new item
            $cart[$productId] = [
                'id' => $productId,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::CART_SESSION_KEY, $cart);

        return $this->getCartSummary();
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(int $productId, int $quantity): array
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                // Remove item if quantity is 0 or negative
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }

            Session::put(self::CART_SESSION_KEY, $cart);
        }

        return $this->getCartSummary();
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $productId): array
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put(self::CART_SESSION_KEY, $cart);
        }

        return $this->getCartSummary();
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): void
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    /**
     * Get cart summary (total count, subtotal, items)
     */
    public function getCartSummary(): array
    {
        $cart = $this->getCart();
        
        $totalCount = 0;
        $subtotal = 0;

        foreach ($cart as $item) {
            $totalCount += $item['quantity'];
            $subtotal += $item['price'] * $item['quantity'];
        }

        return [
            'items' => array_values($cart),
            'total_count' => $totalCount,
            'subtotal' => round($subtotal, 2),
            'item_count' => count($cart),
        ];
    }

    /**
     * Get total items count only (for header badge)
     */
    public function getTotalCount(): int
    {
        $cart = $this->getCart();
        $totalCount = 0;

        foreach ($cart as $item) {
            $totalCount += $item['quantity'];
        }

        return $totalCount;
    }
}
