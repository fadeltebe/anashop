<section class="relative w-full h-[25vh] md:h-[40vh] overflow-hidden mb-1">
    <div id="carousel" class="flex transition-transform duration-700 ease-in-out w-full h-full">
        @foreach ($banners as $banner)
        <div class="flex-shrink-0 w-full h-full relative">
            <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover object-center">

            <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center text-white px-4">
                <!-- <h2 class="text-xl md:text-2xl lg:text-3xl font-bold mb-2">{{ $banner->title }}</h2>
                <p class="text-sm md:text-lg lg:text-xl mb-4">{{ $banner->subtitle }}</p> -->
                @if ($banner->link)
                <a href="{{ $banner->link }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Shop Now
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const carousel = document.getElementById("carousel");
        const slides = carousel.children.length;
        let index = 0;

        setInterval(() => {
            index = (index + 1) % slides;
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }, 3000); // ganti setiap 3 detik
    });
</script>