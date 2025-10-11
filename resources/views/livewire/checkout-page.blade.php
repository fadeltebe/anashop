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

    <form wire:submit.prevent="submit" class="bg-white rounded-xl shadow-md p-6 max-w-lg mx-auto">
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
            <input type="text" wire:model="name" class="w-full border rounded-lg px-3 py-2" />
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nomor HP</label>
            <input type="text" wire:model="phone" class="w-full border rounded-lg px-3 py-2" />
            @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
            <textarea wire:model="address" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
            @error('address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Metode Pembayaran</label>
            <select wire:model="payment_method" class="w-full border rounded-lg px-3 py-2">
                <option value="cod">Bayar di Tempat (COD)</option>
                <option value="transfer">Transfer Bank</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition">
            Lanjutkan Pembayaran
        </button>
    </form>
</div>