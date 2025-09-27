<div class="max-w-7xl mx-auto px-4 py-6">
    <h2 class="text-xl font-bold flex items-center gap-2 mb-4">
        <span class="flex items-center gap-2 bg-red-600 text-white px-3 py-0 rounded-full font-bold animate-blink">
            <span class="relative flex">
                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-white"></span>
            </span>
            <span>LIVE</span>
        </span>
    </h2>

    <x-product-grid :products="$products" />
</div>