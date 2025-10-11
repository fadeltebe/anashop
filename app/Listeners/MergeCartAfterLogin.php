<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeCartAfterLogin
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function handle(Login $event)
    {
        $this->cartService->mergeSessionCart($event->user->id);
    }
}
