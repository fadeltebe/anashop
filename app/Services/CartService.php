<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Str;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /* ======================================================
     * GET OR CREATE CART
     * Untuk user login: pakai user_id
     * Untuk guest: pakai session_id
     * ====================================================== */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            // User login - cari/buat berdasarkan user_id
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );
        }

        // Guest - cari/buat berdasarkan session_id
        $sessionId = Session::get('cart_session_id');

        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            Session::put('cart_session_id', $sessionId);
        }

        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
        );
    }

    /* ======================================================
     * GET CART WITH ITEMS
     * Load cart beserta items dan relasi
     * ====================================================== */
    public function getCartWithItems(): Cart
    {
        $cart = $this->getCart();

        // Eager load untuk performa
        $cart->load([
            'items.product.category',
            'items.variant'
        ]);

        return $cart;
    }

    /* ======================================================
     * ADD ITEM TO CART
     * ====================================================== */
    public function addItem(int $productId, int $variantId, int $quantity = 1): CartItem
    {
        if (!Auth::check()) {
            throw new \Exception('Silakan login terlebih dahulu untuk menambahkan barang ke keranjang.');
        }

        $cart = $this->getCart();

        // Validasi product exists
        $product = Product::findOrFail($productId);

        // Validasi variant exists & belongs to product
        $variant = ProductVariant::where('id', $variantId)
            ->where('product_id', $productId)
            ->firstOrFail();

        // Validasi stok
        if ($variant->stock < $quantity) {
            throw new \Exception(
                "Stok tidak mencukupi. Tersedia: {$variant->stock}, Diminta: {$quantity}"
            );
        }

        // Cek apakah item sudah ada di cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            // Update quantity jika sudah ada
            $newQuantity = $cartItem->quantity + $quantity;

            // Validasi total quantity vs stock
            if ($variant->stock < $newQuantity) {
                throw new \Exception(
                    "Stok tidak mencukupi. Tersedia: {$variant->stock}, " .
                        "Di keranjang: {$cartItem->quantity}, Diminta tambahan: {$quantity}"
                );
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Buat item baru
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        return $cartItem->fresh(['product', 'variant']);
    }

    /* ======================================================
     * UPDATE ITEM QUANTITY
     * ====================================================== */
    public function updateQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = CartItem::findOrFail($cartItemId);

        // Validasi ownership
        if ($cartItem->cart_id !== $this->getCart()->id) {
            throw new \Exception('Item tidak ditemukan di keranjang Anda.');
        }

        // Validasi quantity minimal 1
        if ($quantity < 1) {
            throw new \Exception('Jumlah minimal adalah 1.');
        }

        // Validasi stok
        if ($cartItem->variant->stock < $quantity) {
            throw new \Exception(
                "Stok tidak mencukupi. Tersedia: {$cartItem->variant->stock}"
            );
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh(['product', 'variant']);
    }

    /* ======================================================
     * REMOVE ITEM FROM CART
     * ====================================================== */
    public function removeItem(int $cartItemId): bool
    {
        $cartItem = CartItem::findOrFail($cartItemId);

        // Validasi ownership
        if ($cartItem->cart_id !== $this->getCart()->id) {
            throw new \Exception('Item tidak ditemukan di keranjang Anda.');
        }

        return $cartItem->delete();
    }

    /* ======================================================
     * CLEAR CART
     * Hapus semua items
     * ====================================================== */
    public function clearCart(): bool
    {
        $cart = $this->getCart();
        return $cart->items()->delete();
    }

    /* ======================================================
     * GET CART SUMMARY
     * Total items, subtotal, dll
     * ====================================================== */
    public function getCartSummary(): array
    {
        $cart = $this->getCartWithItems();

        $totalItems = $cart->items->sum('quantity');
        $subtotal = $cart->items->sum(function ($item) {
            return $item->variant->sale_price * $item->quantity;
        });

        return [
            'total_items' => $totalItems,
            'total_unique_items' => $cart->items->count(),
            'subtotal' => $subtotal,
            'tax' => 0, // Sesuaikan jika ada pajak
            'shipping' => 0, // Sesuaikan jika ada ongkir
            'total' => $subtotal,
        ];
    }

    /* ======================================================
     * MERGE GUEST CART TO USER CART
     * Dipanggil saat user login
     * ====================================================== */
    public function mergeGuestCart(string $guestSessionId): void
    {
        if (!Auth::check()) {
            return;
        }

        // Cari guest cart
        $guestCart = Cart::where('session_id', $guestSessionId)->first();

        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        // Get user cart
        $userCart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['session_id' => null]
        );

        // Merge items
        foreach ($guestCart->items as $guestItem) {
            $existingItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $guestItem->product_id)
                ->where('variant_id', $guestItem->variant_id)
                ->first();

            if ($existingItem) {
                // Merge quantity
                $newQuantity = $existingItem->quantity + $guestItem->quantity;

                // Validasi stok
                if ($guestItem->variant->stock >= $newQuantity) {
                    $existingItem->update(['quantity' => $newQuantity]);
                }
            } else {
                // Pindahkan item ke user cart
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        // Hapus guest cart
        $guestCart->delete();
        Session::forget('cart_session_id');
    }

    /* ======================================================
     * GET CART COUNT
     * Total items untuk badge di navbar
     * ====================================================== */
    public function getCartCount(): int
    {
        $cart = $this->getCart();
        return $cart->items->sum('quantity');
    }

    /* ======================================================
     * CHECK IF CART HAS ITEMS
     * ====================================================== */
    public function hasItems(): bool
    {
        return $this->getCart()->items()->exists();
    }

    /* ======================================================
     * SYNC CART PRICES (PRICE CAN CHANGE)
     * ====================================================== */
    public function syncPrices(): void
    {
        $cart = $this->getCartWithItems();

        foreach ($cart->items as $item) {
            $variant = $item->variant;

            if (!$variant) {
                $item->delete();
                continue;
            }

            $latestPrice = $variant->sale_price ?? $variant->cost_price ?? 0;

            if ($item->price != $latestPrice) {
                $item->update(['price' => $latestPrice]);
            }
        }
    }

    /* ======================================================
     * MERGE SESSION CART AFTER LOGIN
     * ====================================================== */
    public function mergeSessionCart(int $userId): void
    {
        $sessionCart = Cart::where('session_id', Session::getId())->first();
        if (!$sessionCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        DB::transaction(function () use ($sessionCart, $userCart) {
            foreach ($sessionCart->items as $item) {

                $variant = ProductVariant::lockForUpdate()->find($item->variant_id);
                if (!$variant || $variant->stock <= 0) {
                    continue;
                }

                $existing = $userCart->items()
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if ($existing) {
                    $qty = min(
                        $existing->quantity + $item->quantity,
                        $variant->stock
                    );
                    $existing->update(['quantity' => $qty]);
                    $item->delete();
                } else {
                    $item->update(['cart_id' => $userCart->id]);
                }
            }

            $sessionCart->delete();
        });
    }

    /* ======================================================
     * VALIDATE STOCK BEFORE CHECKOUT
     * ====================================================== */
    public function validateStock(): array
    {
        $cart = $this->getCartWithItems();
        $errors = [];

        foreach ($cart->items as $item) {
            $stock = $item->variant->stock ?? 0;

            if ($item->quantity > $stock) {
                $errors[] = [
                    'product'   => $item->product->name,
                    'requested' => $item->quantity,
                    'available' => $stock,
                ];
            }
        }

        return $errors;
    }
}
