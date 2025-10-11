<section class="py-2 md:py-2 bg-gray-100">
    <div class="max-w-7xl mx-auto px-2">
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Rekomendasi Produk</h2>
                <!-- <a href="/" class="text-orange-600 hover:text-orange-700">Lihat Semua →</a> -->
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 md:gap-4">

                {{-- Loop menggunakan properti $products dari komponen anak --}}
                @foreach($products as $product)
                {{-- PENTING: wire:key yang unik dan stabil --}}
                <a href="{{ 'product/'.$product->slug }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-200 block" wire:key="product-{{ $product->id }}">

                    <div class="aspect-square flex items-center justify-center p-2">

                        @if($product->thumbnail)
                        <img src="{{ asset('storage/'.$product->thumbnail)}}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                        @else
                        <svg class="w-24 h-24 mx-auto text-orange-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @endif
                    </div>
                    <div class="p-2 md:p-3">
                        <h3 class="font-medium text-gray-900 text-sm md:text-base mb-1 line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        @if(!empty($product->rating))
                        <div class="flex items-center mb-2 text-xs">
                            <div class="flex">
                                <x-rating :value="$product->rating" />
                            </div>
                        </div>
                        @endif


                        <div class="flex flex-col space-y-1">
                            <span class="text-orange-500 font-bold text-sm md:text-base">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            @if($product->discount_price)
                            <span class="text-gray-400 text-xs line-through">
                                Rp{{ number_format($product->discount_price, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>

                        <!-- <div class="flex items-center justify-between mt-2">
                                <span class="text-gray-500 text-xs">Stok: {{ $product->stock ?? 0 }}</span>
                                <span class="text-gray-500 text-xs">Terjual {{ $product->total_sales ?? 0 }}</span>
                            </div> -->
                    </div>
                </a>
                @endforeach
            </div>

            @if($hasMorePages)
            <div x-data {{-- Gunakan x-intersect bawaan Alpine.js untuk memanggil metode Livewire --}} x-intersect.full="$wire.loadMore()" class="text-center py-4 mt-4">

                <div wire:loading.delay wire:target="loadMore" class="text-gray-500">
                    <p>Memuat lebih banyak produk...</p>
                    {{-- Opsional: Tambahkan spinner loading --}}
                </div>
            </div>
            @else
            <div class="text-center py-4 mt-4 text-gray-400">
                Semua produk telah ditampilkan...
            </div>
            @endif
        </div>
    </div>
</section>