<section class="py-1 md:py-2 bg-gray-100">
    <div class="max-w-7xl mx-auto px-2">
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6">
            <div class="flex justify-between items-center mb-2 md:mb-4">
                <h2 class="text-xl font-bold">Kategori Populer</h2>
                <a href="#" class="text-orange-600 hover:text-orange-700 text-sm">Lihat Semua →</a>
            </div>

            <div id="categoriesContainer" class="
                flex overflow-x-auto space-x-3 pb-1 md:pb-2 cursor-grab
                md:grid md:grid-cols-[repeat(auto-fit,minmax(120px,1fr))] md:gap-4 md:overflow-visible md:space-x-0
                scrollbar-hide
            ">
                @foreach($categories as $category)
                <div class="flex-shrink-0 w-20 md:w-full bg-white rounded-lg p-2 md:p-3
                    shadow-sm hover:shadow-md transition-shadow cursor-pointer
                    text-center border border-gray-200">
                    <div class="mb-1 md:mb-2 flex justify-center">
                        @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->nama }}" class="w-10 h-10 md:w-12 md:h-12 object-contain">
                        @else
                        <div class="text-2xl md:text-3xl">📦</div>
                        @endif
                    </div>
                    <h3 class="font-medium text-gray-700 text-xs md:text-sm line-clamp-2">
                        {{ $category->name }}
                    </h3>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
/* For Chrome, Safari, and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* For IE, Edge, and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>