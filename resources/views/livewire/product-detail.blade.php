<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="grid md:grid-cols-2 gap-6">
            {{-- Product Images --}}
            <div class="p-6 bg-gray-50">
                <div class="flex items-center justify-center mb-4">
                    <img src="{{ $mainImage }}" class="rounded-lg object-contain w-full max-h-[400px]" alt="{{ $product->name }}">
                </div>

                @if($availablePhotos->count() > 1)
                <div class="flex space-x-3 overflow-x-auto scrollbar-hide pb-2">
                    @foreach($availablePhotos as $index => $photo)
                    <img src="{{ asset('storage/' . $photo) }}" wire:click="selectPhoto({{ $index }})" class="thumbnail w-20 h-20 object-cover rounded-lg cursor-pointer border-2 transition-all
                            {{ $selectedPhotoIndex === $index ? 'border-orange-500 shadow-md' : 'border-gray-300' }}
                            hover:border-orange-400 hover:shadow-sm" alt="Photo {{ $index + 1 }}">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Product Details --}}
            <div class="p-6 md:p-8">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $product->name }}</h1>
                    <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                </div>

                {{-- Price --}}
                <div class="">
                    <span class="text-3xl font-bold text-orange-600">
                        Rp {{ number_format($product->discount_price ?? $product->price, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mb-4 pb-4 border-b border-gray-200">
                    {{-- Original Price if discount exists --}}
                    @if ($product->discount_price)
                    <span class="text-lg text-gray-500 line-through">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    @endif
                </div>

                {{-- Category --}}
                <div class="mb-6">
                    <span class="inline-block bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full">
                        {{ $product->category->name }}
                    </span>
                </div>

                {{-- Varian --}}
                @if($uniqueVariants->count() > 0)
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Varian:</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($uniqueVariants as $variant)
                        <button wire:click="selectVariant('{{ $variant }}')" class="px-5 py-2.5 rounded-lg border-2 transition-all font-medium
                                   {{ $selectedVariant === $variant ? 'bg-orange-500 text-white border-orange-500 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:border-orange-300 hover:shadow-sm' }}">
                            {{ $variant }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Ukuran --}}
                @if($uniqueSizes->count() > 0)
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Ukuran:</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($uniqueSizes as $size)
                        <button wire:click="selectSize('{{ $size }}')" class="px-5 py-2.5 rounded-lg border-2 transition-all font-medium
                                   {{ $selectedSize === $size ? 'bg-orange-500 text-white border-orange-500 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:border-orange-300 hover:shadow-sm' }}">
                            {{ $size }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Stock --}}
                <div class="mb-5 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        Stok tersedia:
                        <span class="font-bold text-lg {{ $stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stock ?? 0 }}
                        </span>
                    </p>
                </div>

                {{-- Quantity --}}
                @if($stock > 0)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Jumlah:</label>
                    <div class="flex items-center space-x-4">
                        <button wire:click="decreaseQuantity" class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition font-bold text-lg" {{ $quantity <=1 ? 'disabled' : '' }}>
                            -
                        </button>
                        <span class="font-bold text-2xl w-16 text-center">{{ $quantity }}</span>
                        <button wire:click="increaseQuantity" class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition font-bold text-lg" {{ $quantity>= $stock ? 'disabled' : '' }}>
                            +
                        </button>
                    </div>
                </div>
                @endif

                {{-- Flash Messages --}}
                @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
                @endif

                @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button wire:click="addToCart" {{ $stock <=0 ? 'disabled' : '' }} class="flex-1 bg-orange-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-all shadow-md hover:shadow-lg disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none">
                        🛒 Tambah ke Keranjang
                    </button>
                    <a href="{{ route('home') }}" class="flex-1 text-center px-8 py-3 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>