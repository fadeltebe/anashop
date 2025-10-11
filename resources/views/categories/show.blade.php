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
            <div class="border rounded-lg p-3 hover:shadow-md transition">
                <img src="{{ $product->image_url ?? 'https://via.placeholder.com/150' }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded-md mb-2">
                <h2 class="font-semibold text-sm">{{ $product->name }}</h2>
                <p class="text-gray-500 text-xs mb-1">{{ $category->name }}</p>
                <p class="font-bold text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-layouts.app>