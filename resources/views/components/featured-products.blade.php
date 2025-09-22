<section class="py-1 md:py-2 bg-gray-100">
    <div class="max-w-7xl mx-auto px-2">
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-2 md:mb-4">

                <h2 class="text-xl font-bold">⭐ Produk Unggulan</h2>
                <a href="{{ route('featured.all') }}" class="text-sm text-blue-600 hover:underline">
                    Lihat Semua →
                </a>
            </div>

            <div class="flex space-x-4 overflow-x-auto snap-x scrollbar-hide">
                @foreach($products as $product)
                <div class="min-w-[160px] snap-start border rounded-lg p-2 shadow-sm bg-white">
                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/150' }}" alt="{{ $product->name }}" class="h-32 w-full object-cover rounded">
                    <h3 class="text-sm font-semibold mt-2 truncate">{{ $product->name }}</h3>
                    <p class="text-gray-800 font-bold text-sm">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
</section>