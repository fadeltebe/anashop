<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create cart for current user/session
     */
    public function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = Session::getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Add item to cart (with stock validation)
     */
    public function addItem($productId, $quantity = 1, $variant = null, $size = null)
    {
        // 🔹 Ambil produk beserta item varian
        $product = Product::with('items')->findOrFail($productId);
        $cart = $this->getCart();

        // 🔹 Tentukan harga produk (prioritaskan discount_price jika ada)
        $finalPrice = $product->discount_price ?? $product->price;

        // 🔎 Tentukan stok berdasarkan variant/size jika ada
        $itemVariant = $product->items
            ->when($variant, fn($q) => $q->where('variant', $variant))
            ->when($size, fn($q) => $q->where('size', $size))
            ->first();

        $availableStock = $itemVariant ? $itemVariant->stock : $product->stock;

        // 🔒 Validasi stok
        if ($availableStock <= 0) {
            throw new \Exception("Produk '{$product->name}' sedang tidak tersedia.");
        }

        // 🔍 Cek apakah item dengan varian & ukuran sama sudah ada di keranjang
        $existingItem = $cart->items()
            ->where('product_id', $productId)
            ->where('variant', $variant)
            ->where('size', $size)
            ->first();

        // 🧮 Jika item sudah ada, tambahkan jumlah
        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            if ($newQuantity > $availableStock) {
                throw new \Exception("Stok tidak mencukupi untuk '{$product->name}'. Maksimal {$availableStock} item.");
            }

            $existingItem->update(['quantity' => $newQuantity]);
            return $existingItem;
        }

        // 🆕 Jika item baru ditambahkan
        if ($quantity > $availableStock) {
            throw new \Exception("Quantity melebihi stok untuk '{$product->name}'. Maksimal {$availableStock} item.");
        }

        // 💰 Simpan item baru ke keranjang dengan harga final
        return CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $productId,
            'variant'    => $variant,
            'size'       => $size,
            'quantity'   => $quantity,
            'price'      => $finalPrice, // gunakan harga promo jika ada
        ]);
    }


    /**
     * Update item quantity (with stock check)
     */
    public function updateQuantity($cartItemId, $quantity)
    {
        $cartItem = CartItem::with('product.items')->findOrFail($cartItemId);

        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        $product = $cartItem->product;
        $itemVariant = $product->items
            ->when($cartItem->variant, fn($q) => $q->where('variant', $cartItem->variant))
            ->when($cartItem->size, fn($q) => $q->where('size', $cartItem->size))
            ->first();

        $availableStock = $itemVariant ? $itemVariant->stock : $product->stock;

        if ($quantity > $availableStock) {
            throw new \Exception("Stok tidak mencukupi untuk '{$product->name}'. Maksimal $availableStock item.");
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cartItem->delete();
        return true;
    }

    /**
     * Clear entire cart
     */
    public function clearCart()
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        return true;
    }

    /**
     * Get cart with items
     */
    public function getCartWithItems()
    {
        return $this->getCart()->load('items.product');
    }

    /**
     * Merge session cart to user cart after login
     */
    public function mergeSessionCart($userId)
    {
        $sessionId = Session::getId();
        $sessionCart = Cart::where('session_id', $sessionId)->first();

        if (!$sessionCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($sessionCart->items as $sessionItem) {
            $existingItem = $userCart->items()
                ->where('product_id', $sessionItem->product_id)
                ->where('variant', $sessionItem->variant)
                ->where('size', $sessionItem->size)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $sessionItem->quantity;

                // 🔒 Validasi stok saat merge
                $product = $sessionItem->product;
                $variantItem = $product->items
                    ->when($sessionItem->variant, fn($q) => $q->where('variant', $sessionItem->variant))
                    ->when($sessionItem->size, fn($q) => $q->where('size', $sessionItem->size))
                    ->first();

                $stock = $variantItem ? $variantItem->stock : $product->stock;

                if ($newQuantity > $stock) {
                    $newQuantity = $stock; // Sesuaikan dengan stok maksimum
                }

                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                $sessionItem->update(['cart_id' => $userCart->id]);
            }
        }

        $sessionCart->delete();
    }

    /**
     * Get cart count
     */
    public function getCartCount()
    {
        $cart = $this->getCart();
        return $cart->items()->sum('quantity') ?? 0;
    }

    /**
     * Validate stock before checkout
     */
    public function validateStock()
    {
        $cart = $this->getCartWithItems();
        $errors = [];

        foreach ($cart->items as $item) {
            $product = $item->product;
            $variantItem = $product->items
                ->when($item->variant, fn($q) => $q->where('variant', $item->variant))
                ->when($item->size, fn($q) => $q->where('size', $item->size))
                ->first();

            $availableStock = $variantItem ? $variantItem->stock : $product->stock;

            if ($item->quantity > $availableStock) {
                $errors[] = [
                    'product' => $product->name,
                    'requested' => $item->quantity,
                    'available' => $availableStock,
                ];
            }
        }

        return $errors;
    }
}
