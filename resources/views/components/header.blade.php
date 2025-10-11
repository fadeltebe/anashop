@php
$setting = \App\Models\Setting::first();
@endphp

<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <img src="{{ $setting->logo ? asset('storage/'.$setting->logo) : 'https://via.placeholder.com/100' }}" alt="{{ $setting->store_name }}" class="h-10 w-auto">
                <span class="hidden sm:block text-lg" style="font-family: 'Bodoni Moda', serif; color:#800000; font-weight:600;">
                    {{ $setting->store_name }}
                </span>
            </a>

            <!-- Search -->
            <div class="flex-1 max-w-lg mx-2 md:mx-8">
                <form method="GET" action="{{ route('search') }}">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Navigation -->
            <nav class="flex items-center space-x-2 md:space-x-4">
                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="relative p-2 text-gray-600 hover:text-orange-600">
                    <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>

                <!-- Cart Badge (Livewire Component) -->
                @livewire('cart-badge')

                <!-- Login -->
                <a href="{{ route('login') }}" class="bg-orange-500 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                    Masuk
                </a>
            </nav>
        </div>
    </div>
</header>