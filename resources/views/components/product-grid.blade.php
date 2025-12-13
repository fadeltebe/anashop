<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
    @forelse($products as $product)
    <x-product-card :product="$product" />
    @empty
    <p class="col-span-full text-center text-gray-500">Belum ada produk tersedia.</p>
    @endforelse
</div>

{{-- Load More --}}
<div class="mt-6 flex justify-center">
    @if($products->hasMorePages())
    <div x-data x-intersect.full="$wire.loadMore()" class="text-center py-4 mt-4">
        <div wire:loading.delay wire:target="loadMore" class="text-gray-500">
            <p>Memuat lebih banyak produk...</p>
        </div>
    </div>
    @else
    <div class="text-center py-4 mt-4 text-gray-400">
        Semua produk telah ditampilkan...
    </div>
    @endif
</div>