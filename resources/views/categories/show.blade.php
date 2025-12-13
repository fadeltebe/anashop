<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{{ $category->name }}</h1>
            <a href="{{ route('categories.index') }}" class="text-orange-600 hover:underline text-sm">← Kembali ke semua kategori</a>
        </div>

        @if ($products->isEmpty())
        <p class="text-gray-500">Belum ada produk di kategori ini.</p>
        @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($products as $product)
            <x-product-card :product="$product" />
            @endforeach
        </div>
        @endif
    </div>
</x-layouts.app>