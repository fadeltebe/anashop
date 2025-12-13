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
                <x-product-card :product="$product" :wire:key="'recommended-product-'.$product->id" />
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