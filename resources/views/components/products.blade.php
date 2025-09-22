<section class="py-2 md:py-2 bg-gray-100">
    <div class="max-w-7xl mx-auto px-2">
        <!-- Card Besar -->
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Produk Unggulan</h2>
                <a href="/" class="text-orange-600 hover:text-orange-700">Lihat Semua →</a>
            </div>

            <!-- Grid Produk -->
            <div id="product-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($products as $product)
                <a href="#" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-200 block">
                    <div class="aspect-square flex items-center justify-center">
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/default-product.png') }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                    </div>
                    <div class="p-2 md:p-3">
                        <h3 class="font-medium text-gray-900 text-sm md:text-base mb-1 line-clamp-2">
                            {{ $product->name }}
                        </h3>

                        {{-- Rating Produk (opsional, kalau ada kolom rating di DB) --}}
                        @if(!empty($product->rating))
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 text-xs">
                                @for($i = 0; $i < floor($product->rating); $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.959a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.959c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.785.57-1.84-.196-1.54-1.118l1.287-3.959a1 1 0 00-.364-1.118L2.075 9.386c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.959z" />
                                    </svg>
                                    @endfor
                            </div>
                            <span class="text-gray-500 text-xs ml-1">({{ number_format($product->rating, 1) }})</span>
                        </div>
                        @endif

                        {{-- Harga --}}
                        <div class="flex flex-col space-y-1">
                            <span class="text-orange-500 font-bold text-sm md:text-base">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            @if($product->discount_price)
                            <span class="text-gray-400 text-xs line-through">
                                Rp{{ number_format($product->discount_price, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>

                        {{-- Info stok --}}
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-gray-500 text-xs">Stok: {{ $product->stok ?? 0 }}</span>
                            <span class="text-gray-500 text-xs">Terjual {{ $product->terjual ?? 0 }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Infinite scroll loader --}}
            <div id="loading" class="text-center py-4 hidden">Loading...</div>
        </div>
    </div>
</section>


@push('scripts')
<script>
    let skip = 12, limit = 12, loading = false, finished = false;

    function renderStars(rating) {
        let stars = '';
        let fullStars = Math.floor(rating);
        let halfStar = (rating - fullStars) >= 0.5;

        for (let i = 0; i < fullStars; i++) {
            stars += `<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.959a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.959c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.785.57-1.84-.196-1.54-1.118l1.287-3.959a1 1 0 00-.364-1.118L2.075 9.386c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.959z"/></svg>`;
        }
        if (halfStar) {
            stars += `<svg class="w-4 h-4 fill-current opacity-50" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.959a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.959c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.785.57-1.84-.196-1.54-1.118l1.287-3.959a1 1 0 00-.364-1.118L2.075 9.386c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.959z"/></svg>`;
        }
        return stars;
    }

    async function loadProducts() {
        if (loading || finished) return;
        loading = true;
        document.getElementById('loading').classList.remove('hidden');

        const response = await fetch(`/dummy-products/load?skip=${skip}&limit=${limit}`);
        const products = await response.json();

        if (products.length === 0) {
            finished = true;
            document.getElementById('loading').innerText = "Semua produk sudah tampil.";
            return;
        }

        const container = document.getElementById('product-container');
        products.forEach(p => {
            const card = document.createElement('a');
            card.href = `/products/${p.id}`;
            card.className = "bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-200 block";
            card.innerHTML = `
                <div class="aspect-square flex items-center justify-center">
                    <img src="${p.thumbnail}" alt="${p.title}" class="object-cover w-full h-full">
                </div>
                <div class="p-2 md:p-3">
                    <h3 class="font-medium text-gray-900 text-sm md:text-base mb-1 line-clamp-2">${p.title}</h3>
                    <div class="flex items-center mb-2">
                        <div class="flex text-yellow-400 text-xs">${renderStars(p.rating)}</div>
                        <span class="text-gray-500 text-xs ml-1">(${p.rating.toFixed(1)})</span>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <span class="text-orange-500 font-bold text-sm md:text-base">Rp${(p.price * 15000).toLocaleString('id-ID')}</span>
                        <span class="text-gray-400 text-xs line-through">Rp${(p.price * 18000).toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-gray-500 text-xs">${p.stock ?? 0}</span>
                        <span class="text-gray-500 text-xs">Terjual 100</span>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        skip += limit;
        loading = false;
        document.getElementById('loading').classList.add('hidden');
    }

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
            loadProducts();
        }
    });
</script>
@endpush