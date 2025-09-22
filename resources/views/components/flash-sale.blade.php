<section class="py-1 md:py-2 bg-gray-100">
    <div class="max-w-7xl mx-auto px-2">
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-2 md:mb-4">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <img src="/images/fire.gif" alt="flame" class="w-6 h-6">
                    Flash Sale
                </h2>
                <a href="{{ route('flash-sale.all') }}" class="text-orange-600 hover:text-orange-700 text-sm">
                    Lihat Semua →
                </a>
            </div>

            <!-- List produk flash sale scroll horizontal -->
            <div id="flashSaleContainer" class="flex overflow-x-auto space-x-3 scrollbar-hide pb-1 md:pb-2 cursor-grab">
                @foreach($flashSales as $product)
                <div class="flex-shrink-0 w-40 md:w-48 bg-white rounded-lg p-2 md:p-3 shadow-sm hover:shadow-md transition-shadow border border-gray-200">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-28 md:h-32 object-cover rounded-md mb-2">
                    <h3 class="font-medium text-gray-700 text-sm line-clamp-2">
                        {{ $product->name }}
                    </h3>
                    <div class="mt-1">
                        <span class="text-red-600 font-bold text-sm">
                            Rp{{ number_format($product->discount_price, 0, ',', '.') }}
                        </span>
                        <span class="text-gray-400 line-through text-xs ml-1">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</section>