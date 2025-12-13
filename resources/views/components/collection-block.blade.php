<section class="py-1 md:py-2 bg-gray-100">
    <div class="max-w-7xl mx-auto px-2">
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-2 md:mb-4">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    @if($collection->icon)
                    <img src="{{ asset('storage/'.$collection->icon) }}" class="h-6">
                    @endif
                    {{ $collection->name }}
                </h2>

                <a href="{{ route('collection.show', $collection->slug) }}" class="text-orange-600 hover:text-orange-700 text-sm">
                    Lihat Semua →
                </a>
            </div>

            <!-- Product Items -->
            <div class="flex overflow-x-auto space-x-3 scrollbar-hide pb-1 md:pb-2 cursor-grab">

                @foreach($collection->items as $item)
                @php $product = $item->product; @endphp
                @if($product)
                <x-product-card :product="$product" />
                @endif

                @endforeach

            </div>

        </div>
    </div>
</section>