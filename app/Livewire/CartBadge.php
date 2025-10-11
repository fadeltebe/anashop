<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;

class CartBadge extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCartCount'];

    public function mount(CartService $cartService)
    {
        $this->cartCount = $cartService->getCartCount();
    }

    public function updateCartCount()
    {
        $cartService = app(CartService::class);
        $this->cartCount = $cartService->getCartCount();
    }

    public function render()
    {
        return view('livewire.cart-badge');
    }
}
