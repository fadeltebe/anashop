<section class="overflow-x-auto scrollbar-hide snap-x snap-mandatory flex">
    @foreach($banners as $banner)
    <div class="snap-center flex-shrink-0 w-full h-[20vh] md:h-[40vh] relative overflow-hidden">
        <a href="{{ route('banner.show', $banner->slug) }}">
            <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
        </a>
    </div>
    @endforeach
</section>

<script>
    const slider = document.querySelector('section');
    let scrollAmount = 0;
    const slideWidth = slider.querySelector('div').offsetWidth + 16; // 16 = space-x-4

    setInterval(() => {
        scrollAmount += slideWidth;
        if (scrollAmount >= slider.scrollWidth) scrollAmount = 0;
        slider.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }, 4000);
</script>