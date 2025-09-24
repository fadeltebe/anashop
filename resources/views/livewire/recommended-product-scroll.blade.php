<section class="py-2 md:py-2 bg-gray-100">
    <div class="max-w-7xl mx-auto px-2">
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Rekomendasi Produk</h2>
                <a href="/" class="text-orange-600 hover:text-orange-700">Lihat Semua →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
                {{-- Loop menggunakan properti $products dari komponen anak --}}
                @foreach($products as $product)
                {{-- PENTING: wire:key yang unik dan stabil --}}
                <a href="{{ $product->slug }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-200 block" wire:key="product-{{ $product->id }}">

                    <div class="aspect-square flex items-center justify-center">
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/default-product.png') }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                    </div>
                    <div class="p-2 md:p-3">
                        <h3 class="font-medium text-gray-900 text-sm md:text-base mb-1 line-clamp-2">
                            {{ $product->name }}
                        </h3>

                        {{-- ... Rating, Harga, Diskon, Stok, Terjual (kode sama seperti sebelumnya) ... --}}
                        @if(!empty($product->rating))
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 text-xs">
                                @for($i = 0; $i < floor($product->rating); $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c..." />
                                    </svg>
                                    @endfor
                            </div>
                            <span class="text-gray-500 text-xs ml-1">({{ number_format($product->rating, 1) }})</span>
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

                        <div class="flex items-center justify-between mt-2">
                            <span class="text-gray-500 text-xs">Stok: {{ $product->stok ?? 0 }}</span>
                            <span class="text-gray-500 text-xs">Terjual {{ $product->terjual ?? 0 }}</span>
                        </div>
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
                Semua produk telah termuat.
            </div>
            @endif
        </div>
    </div>
</section>