<div class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-inner flex justify-around items-center py-3 text-sm text-gray-600 md:hidden">

    <!-- Home -->
    <a href="{{ route('home') }}" class="flex flex-col items-center hover:text-orange-500 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75V21a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 21V9.75z" />
        </svg>
        <span class="text-xs">Beranda</span>
    </a>

    <!-- Kategori -->
    <a href="{{ route('categories.index') }}" class="flex flex-col items-center hover:text-orange-500 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span class="text-xs">Kategori</span>
    </a>

    <!-- Keranjang -->
    <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center hover:text-orange-500 transition">
        <svg class="h-6 w-6 md:h-6 mb-0.5 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span class="text-xs">Keranjang</span>
    </a>

    <!-- Profil -->
    <a href="{{ route('profile') }}" class="flex flex-col items-center hover:text-orange-500 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a9 9 0 0114 0H5z" />
        </svg>
        <span class="text-xs">Profil</span>
    </a>
</div>