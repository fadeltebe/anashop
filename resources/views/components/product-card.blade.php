@props(['product'])

<a href="{{ url('product/' . $product->slug) }}" {{ $attributes->merge([
    'class' => 'bg-white rounded-lg p-2 md:p-3 shadow-sm hover:shadow-md transition-shadow border border-gray-200 block'
    ]) }}>

    {{-- Thumbnail --}}
    <div class="w-full h-32 md:h-40 overflow-hidden rounded-md mb-2 bg-gray-50">
        @if($product->thumbnail)
        <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
        <div class="w-full h-full flex items-center justify-center">
            <svg class="w-16 h-16 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        @endif
    </div>

    {{-- Nama Produk --}}
    <h3 class="font-medium text-gray-700 text-sm line-clamp-2 mb-1">
        {{ $product->name }}
    </h3>

    {{-- Rating --}}
    @if(!empty($product->rating))
    <div class="flex items-center mb-1 text-xs">
        <x-rating :value="$product->rating" />
    </div>
    @endif

    {{-- Harga --}}
    <div class="flex flex-col space-y-0.5">
        @if($product->has_variant)
        <span class="text-xs text-gray-500">
            Mulai dari
        </span>
        @endif

        <span class="text-orange-500 font-bold text-sm md:text-base">
            {{ $product->display_price }}
        </span>
    </div>

</a>