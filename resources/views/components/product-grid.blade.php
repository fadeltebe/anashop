<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" {{ $attributes->merge(['wire:poll.5s' => true]) }}>
    @forelse($products as $product)
    <a href="{{ url('product/'.$product->slug) }}" class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <div class="aspect-square">
            @if($product->thumbnail)
            <img src="{{ asset('storage/'.$product->thumbnail)}}" alt="{{ $product->name }}" class="w-full h-28 md:h-32 object-cover rounded-md mb-2">
            @else
            <svg class="w-24 h-24 mx-auto text-orange-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            @endif
        </div>
        <div class="mt-2">
            <h3 class="text-sm font-semibold line-clamp-2">{{ $product->name }}</h3>

            @if(!empty($product->rating))
            <div class="flex items-center mb-2 text-xs">
                <div class="flex">
                    <x-rating :value="$product->rating" />
                </div>
            </div>
            @endif

            {{-- Harga (support diskon & normal) --}}
            @if(!empty($product->discount_price))
            <div class="text-orange-600 font-bold">
                Rp{{ number_format($product->discount_price, 0, ',', '.') }}
            </div>
            <div class="text-gray-400 line-through text-sm">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </div>
            @else
            <div class="text-orange-600 font-bold">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </div>
            @endif
        </div>
    </a>
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