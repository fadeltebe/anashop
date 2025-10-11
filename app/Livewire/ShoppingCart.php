<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use Illuminate\Support\Collection;

class ShoppingCart extends Component
{
    public Collection $cartItems;
    public $subtotal = 0;
    public $shipping = 0;
    public $total = 0;

    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->cartItems = collect();
        $this->loadCart();
    }

    public function loadCart()
    {
        $cart = $this->cartService->getCartWithItems();

        // ✅ Biarkan tetap sebagai Eloquent Collection
        $this->cartItems = $cart->items->load('product');

        $this->calculateTotals();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        // Validasi quantity minimum
        if ($quantity < 1) {
            session()->flash('error', 'Jumlah minimal adalah 1');
            return;
        }

        try {
            $this->cartService->updateQuantity($cartItemId, $quantity);
            $this->loadCart();

            // 🔔 Emit event ke CartBadge
            $this->dispatch('cartUpdated');

            session()->flash('success', 'Jumlah berhasil diupdate');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal update jumlah: ' . $e->getMessage());
        }
    }

    public function removeItem($cartItemId)
    {
        try {
            $this->cartService->removeItem($cartItemId);
            $this->loadCart();
            session()->flash('success', 'Item berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        try {
            $this->cartService->clearCart();
            $this->loadCart();
            session()->flash('success', 'Keranjang berhasil dikosongkan');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengosongkan keranjang: ' . $e->getMessage());
        }
    }

    protected function calculateTotals()
    {
        $this->subtotal = $this->cartItems->sum(function ($item) {
            $price = $item->product->discount_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        $this->shipping = $this->subtotal > 100000 ? 0 : 15000;
        $this->total = $this->subtotal + $this->shipping;
    }


    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
