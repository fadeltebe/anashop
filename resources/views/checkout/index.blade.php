<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-orange-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Halaman Checkout</h2>
                <p class="text-gray-600 mb-6">Fitur checkout sedang dalam pengembangan</p>
                <a href="{{ route('cart.index') }}" class="inline-block bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition">
                    ← Kembali ke Keranjang
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>