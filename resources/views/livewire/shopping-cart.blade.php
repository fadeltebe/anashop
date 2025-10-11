<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Keranjang Belanja</h1>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        @if($cartItems->count() > 0)
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex gap-4">
                            {{-- Product Image --}}
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="w-24 h-24 object-cover rounded-lg">
                            </div>

                            {{-- Product Info --}}
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $item->product->name }}
                                </h3>
                                <div class="mt-2">
                                    @if ($item->product->discount_price)
                                    <p class="text-lg font-bold text-orange-600">
                                        Rp {{ number_format($item->product->discount_price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-sm text-gray-500 line-through">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </p>
                                    @else
                                    <p class="text-lg font-bold text-orange-600">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </p>
                                    @endif
                                </div>


                                {{-- Quantity Controls --}}
                                <div class="flex items-center space-x-3 mt-4">
                                    <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 border-2 border-gray-300 rounded-lg hover:bg-gray-100 transition" @if($item->quantity <= 1) disabled class="opacity-50 cursor-not-allowed" @endif>
                                            -
                                    </button>
                                    <span class="font-bold w-8 text-center">{{ $item->quantity }}</span>
                                    <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-8 h-8 border-2 border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                        +
                                    </button>
                                </div>
                            </div>

                            {{-- Remove Button --}}
                            <button wire:click="removeItem({{ $item->id }})" class="text-red-500 hover:text-red-700 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Ongkir</span>
                            <span class="font-semibold">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('checkout') }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center py-3 rounded-lg font-semibold transition">
                        Checkout
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-16">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Keranjang Kosong</h2>
            <p class="text-gray-600 mb-6">Belum ada produk di keranjang Anda</p>
            <a href="{{ route('home') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg transition">
                Mulai Belanja
            </a>
        </div>
        @endif
    </div>
</div>