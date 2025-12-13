<div class="max-w-7xl mx-auto px-4 py-6">
    <h2 class="text-xl font-bold flex items-center gap-2 mb-4">
        <img src="{{ asset('storage/'.$collection->icon) }}" class="h-6">
        {{ $collection->name }}

    </h2>

    <x-product-grid :products="$products" />
</div>