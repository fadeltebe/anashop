<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Daftar Produk -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>

                @if($cart && $cart->items->count() > 0)
                <div class="space-y-4">
                    @foreach($cart->items as $item)
                    <div class="border-b pb-4">
                        <div class="flex items-start gap-4">
                            @if($item->product->primary_image)
                            <img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->product_name }}" class="w-20 h-20 object-cover rounded">
                            @else
                            <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-gray-400 text-xs">No Image</span>
                            </div>
                            @endif

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                @if($item->variant_name)
                                <p class="text-sm text-gray-600">Varian: {{ $item->variant_name }}</p>
                                @endif
                                <p class="text-sm text-gray-600">SKU: {{ $item->sku ?? 'N/A' }}</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm text-gray-600">Qty: {{ $item->quantity }}</span>
                                    <span class="font-semibold text-gray-900">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-right">
                            <p class="font-semibold text-gray-900">
                                Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-600 text-center py-8">Keranjang Anda kosong</p>
                @endif
            </div>
        </div>

        <!-- Form Pembayaran -->
        <div>
            <form wire:submit.prevent="submit" class="bg-white rounded-xl shadow-md p-6 sticky top-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Pembayaran</h2>

                <!-- Total Pesanan -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Diskon:</span>
                        <span class="font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Biaya Tambahan:</span>
                        <span class="font-semibold">Rp 0</span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">Total:</span>
                            <span class="font-bold text-lg text-orange-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-3">Pilih Metode Pembayaran</label>
                    <select wire:model="payment_method" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:border-orange-500 focus:outline-none">
                        <option value="" selected disabled>-- Pilih Metode --</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="e_wallet">E-Wallet</option>
                        <option value="cod">Cash on Delivery (COD)</option>
                    </select>
                    @error('payment_method')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition">
                    Lanjutkan Pembayaran
                </button>

                <a href="{{ route('cart.index') }}" class="block text-center mt-3 text-gray-600 hover:text-gray-900 font-semibold">
                    ← Kembali ke Keranjang
                </a>
            </form>
        </div>
    </div>
</div>