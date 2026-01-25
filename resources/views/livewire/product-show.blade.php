<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="grid md:grid-cols-2 gap-6">

            {{-- ========================================
            PRODUCT IMAGES SECTION
            ======================================== --}}
            <div class="p-6 bg-gray-50">
                {{-- Main Image --}}
                <div class="flex items-center justify-center mb-4 bg-white rounded-lg p-4">
                    <img src="{{ $mainImage }}" class="rounded-lg object-contain w-full max-h-[400px]" alt="{{ $product->name }}">
                </div>

                {{-- Thumbnail Gallery --}}
                @if($availablePhotos->count() > 1)
                <div class="flex space-x-3 overflow-x-auto scrollbar-hide pb-2">
                    @foreach($availablePhotos as $index => $photo)
                    <div class="relative flex-shrink-0">
                        <img src="{{ asset('storage/' . $photo['url']) }}" wire:click="selectPhoto({{ $index }})" class="thumbnail w-20 h-20 object-cover rounded-lg cursor-pointer border-2 transition-all
                                {{ $selectedPhotoIndex === $index ? 'border-orange-500 shadow-md ring-2 ring-orange-200' : 'border-gray-300' }}
                                hover:border-orange-400 hover:shadow-sm" alt="Photo {{ $index + 1 }}">

                        {{-- Badge untuk foto variant --}}
                        @if($photo['type'] === 'variant')
                        <div class="absolute -top-1 -right-1 bg-orange-500 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full">
                            V
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Legend --}}
                @if($variantPhotos->count() > 0)
                <div class="mt-2 text-xs text-gray-500 flex items-center gap-2">
                    <span class="inline-flex items-center">
                        <span class="w-3 h-3 bg-orange-500 rounded-full mr-1"></span>
                        Foto Varian
                    </span>
                    <span class="inline-flex items-center">
                        <span class="w-3 h-3 bg-gray-300 rounded-full mr-1"></span>
                        Foto Produk
                    </span>
                </div>
                @endif
                @endif
            </div>

            {{-- ========================================
            PRODUCT DETAILS SECTION
            ======================================== --}}
            <div class="p-6 md:p-8">

                {{-- Product Name & Description --}}
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">
                        {{ $product->name }}
                    </h1>

                    @if($product->description)
                    <p class="text-gray-600 leading-relaxed">
                        {{ $product->description }}
                    </p>
                    @endif
                </div>

                {{-- Price --}}
                <div class="mb-2">
                    <span class="text-3xl font-bold text-orange-600">
                        Rp {{ number_format($activeVariant?->sale_price ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Compare Price --}}
                @if($activeVariant && isset($activeVariant->compare_price) && $activeVariant->compare_price > $activeVariant->sale_price)
                <div class="mb-4 pb-4 border-b border-gray-200">
                    <span class="text-lg text-gray-500 line-through">
                        Rp {{ number_format($activeVariant->compare_price, 0, ',', '.') }}
                    </span>
                    <span class="ml-2 text-sm font-semibold text-red-600 bg-red-100 px-2 py-1 rounded">
                        Hemat {{ number_format((($activeVariant->compare_price - $activeVariant->sale_price) / $activeVariant->compare_price) * 100, 0) }}%
                    </span>
                </div>
                @endif

                {{-- Category --}}
                <div class="mb-6">
                    <span class="inline-block bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full">
                        📁 {{ $product->category->name }}
                    </span>
                </div>

                {{-- Variant Selection --}}
                @if($product->has_variant && $availableVariants->count() > 0)
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Pilih Varian:
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($availableVariants as $variant)
                        <button wire:click="selectVariant({{ $variant->id }})" class="group relative px-3 py-1.5 rounded-md border text-sm transition-all font-medium
        {{ $activeVariant && $activeVariant->id === $variant->id 
            ? 'bg-orange-500 text-white border-orange-500 shadow-sm ring-2 ring-orange-200' 
            : 'bg-white text-gray-700 border-gray-300 hover:border-orange-400 hover:shadow-sm hover:bg-orange-50' }}
        {{ $variant->stock <= 0 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}" {{ $variant->stock <= 0 ? 'disabled' : '' }}>

                                {{-- Badge jika variant punya foto --}}
                                @if($variant->image)
                                <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-orange-500 rounded-full flex items-center justify-center">
                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                @endif

                                <span class="block">{{ $variant->variant_name }}</span>

                                {{-- Tampilkan harga jika berbeda --}}
                                @if($variant->sale_price !== $activeVariant?->sale_price)
                                <span class="block text-xs mt-0.5 opacity-75">
                                    Rp {{ number_format($variant->sale_price, 0, ',', '.') }}
                                </span>
                                @endif

                                {{-- Badge stok habis --}}
                                @if($variant->stock <= 0) <span class="block text-xs mt-0.5 text-red-600 font-semibold">Habis</span>
                                    @endif
                        </button>
                        @endforeach
                    </div>

                    @if($variantPhotos->count() > 0)
                    <p class="text-xs text-gray-500 mt-2">
                        💡 Tip: Klik foto varian di galeri untuk langsung memilih varian tersebut
                    </p>
                    @endif
                </div>
                @endif

                {{-- Stock Info --}}
                <div class="mb-5 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            Stok tersedia:
                            <span class="font-bold text-lg {{ $stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $stock > 0 ? $stock : 'Habis' }}
                            </span>
                        </p>

                        <!-- @if($activeVariant)
                        <p class="text-xs text-gray-500">
                            SKU: <span class="font-mono">{{ $activeVariant->sku }}</span>
                        </p>
                        @endif -->
                    </div>
                </div>

                {{-- Quantity Selector --}}
                @if($stock > 0)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Jumlah:
                    </label>
                    <div class="flex items-center space-x-4">
                        <button wire:click="decreaseQuantity" class="w-12 h-12 flex items-center justify-center border-2 border-gray-300 rounded-lg 
                                   hover:border-orange-500 hover:bg-orange-50 transition font-bold text-xl
                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-300 disabled:hover:bg-white" {{ $quantity <=1 ? 'disabled' : '' }}>
                            −
                        </button>

                        <span class="font-bold text-2xl w-20 text-center">{{ $quantity }}</span>

                        <button wire:click="increaseQuantity" class="w-12 h-12 flex items-center justify-center border-2 border-gray-300 rounded-lg 
                                   hover:border-orange-500 hover:bg-orange-50 transition font-bold text-xl
                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-300 disabled:hover:bg-white" {{ $quantity>= $stock ? 'disabled' : '' }}>
                            +
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Maksimal pembelian: {{ $stock }} pcs
                    </p>
                </div>
                @endif

                {{-- Flash Messages --}}
                @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg animate-pulse">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button wire:click="addToCart" {{ $stock <=0 ? 'disabled' : '' }} class="flex-1 bg-orange-500 text-white px-8 py-3 rounded-lg font-semibold 
                               hover:bg-orange-600 active:bg-orange-700 transition-all shadow-md hover:shadow-lg 
                               disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none
                               flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Tambah ke Keranjang</span>
                    </button>

                    <button wire:click="buyNow" {{ $stock <=0 ? 'disabled' : '' }} class="flex-1 px-8 py-3 rounded-lg bg-green-500 text-white font-semibold 
                               hover:bg-green-600 active:bg-green-700 transition-all shadow-md hover:shadow-lg 
                               disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none
                               flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Beli Sekarang</span>
                    </button>
                </div>

                {{-- Additional Info --}}
                @if($stock <= 0) <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800 text-center">
                        ⚠️ Produk ini sedang habis. Silakan pilih varian lain atau hubungi kami.
                    </p>
            </div>
            @endif
        </div>
    </div>
</div>
</div>