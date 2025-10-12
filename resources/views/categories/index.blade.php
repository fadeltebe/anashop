<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Semua Kategori</h1>

        @if ($categories->isEmpty())
        <p class="text-gray-500">Belum ada kategori yang tersedia.</p>
        @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}" class="block bg-white shadow rounded-xl p-4 hover:shadow-lg transition">
                <img src="{{ asset('storage/' . $category->icon) ?? 'https://via.placeholder.com/150' }}" alt="{{ $category->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                <h2 class="font-semibold text-gray-800 text-center">{{ $category->name }}</h2>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</x-layouts.app>