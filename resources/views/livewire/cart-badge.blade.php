<a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-orange-600 transition-colors">
    <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>

    @if($cartCount > 0)
    <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full h-4 w-4 md:h-5 md:w-5 flex items-center justify-center font-semibold">
        {{ $cartCount > 99 ? '99+' : $cartCount }}
    </span>
    @endif
</a>