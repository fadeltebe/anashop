<div class="flex flex-col md:flex-row w-full bg-white rounded-lg shadow p-2">
    <!-- Kolom Kiri: Pencarian & Daftar Produk -->
    <div class="w-full md:w-3/4 p-4 shadow-md mt-4 md:mt-0">

        <!-- Pencarian Produk -->
        <div class="relative mb-2">
            <input wire:model.live.debounce.500ms="search" wire:click="tampilproduk" wire:blur="hide" type="text" class="block w-full flex-1 py-2 px-3 outline-none border-primary rounded-md" placeholder="Cari Barang..." autocomplete="off" />

            @if(count($produks) > 0)
            <div class="absolute w-full overflow-hidden rounded-md bg-white border z-10">
                @foreach($produks as $produk)
                <div class="cursor-pointer py-2 px-3 hover:bg-slate-100" wire:click="pilihProduk({{ $produk->id }})">
                    <div class="grid grid-cols-3">
                        <div class="text-sm font-medium text-gray-600">
                            {{ $produk->name }}
                        </div>
                        <div class="text-sm font-medium text-gray-600">
                            Rp. {{ number_format($produk->harga, 0, ',', '.') }}
                        </div>
                        <div class="text-sm font-medium text-gray-600">
                            Stok: {{ $produk->stok }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Daftar Belanja -->
        <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 mt-4">
            <div class="flex items-center justify-between mb-4">
                <p class="font-bold leading-none text-gray-900">Daftar Belanja</p>
            </div>
            <div class="flow-root">
                <div class="max-h-80 overflow-y-auto">
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach($produkTerpilih as $item)
                        <li class="py-3 sm:py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 p-2">
                                    <img class="w-16 h-16 rounded-lg object-cover" src="{{ asset('storage/' . $item['image']) }}">
                                </div>
                                <div class="flex-1 min-w-0 ms-4">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $item['name'] }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center rounded-lg">
                                    <div class="inline-flex rounded-lg" role="group">
                                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 disabled:opacity-50" wire:click="kurangProduk({{ $item['id'] }})" @if($item['jumlah'] <=1) disabled @endif>
                                            -
                                        </button>

                                        <span class="px-4 py-2 text-sm font-medium text-gray-900">
                                            {{ $item['jumlah'] }}
                                        </span>

                                        <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-500 border border-gray-200 rounded-e-lg hover:bg-green-600" wire:click="tambahProduk({{ $item['id'] }})">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Form Pembayaran -->
    <div class="w-full md:w-1/4 p-4 shadow-md mt-4 md:mt-0">
        <div class="mt-4 space-y-3">
            <input wire:model="name" type="text" class="block w-full p-2 border border-gray-300 rounded-md" placeholder="Nama Pembeli">

            <div class="flex items-center space-x-3">
                <label class="flex items-center">
                    <input wire:model="gender" type="radio" value="Laki-laki" class="mr-2"> Laki-laki
                </label>
                <label class="flex items-center">
                    <input wire:model="gender" type="radio" value="Perempuan" class="mr-2"> Perempuan
                </label>
            </div>

            <input wire:model="no_hp" type="number" class="block w-full p-2 border border-gray-300 rounded-md" placeholder="Nomor HP">

            <textarea wire:model="alamat" class="block w-full p-2 border border-gray-300 rounded-md" placeholder="Alamat"></textarea>

            <select wire:model="metode_pembayaran" class="block w-full p-2 border border-gray-300 rounded-md">
                <option value="">Pilih Metode Pembayaran</option>
                <option value="Cash">Cash</option>
                <option value="Kartu Kredit">Kartu Kredit</option>
                <option value="Transfer Bank">Transfer Bank</option>
            </select>

            <h4 class="mt-4 font-bold">
                Total Belanja: Rp{{ number_format($grand_total, 0, ',', '.') }}
            </h4>

            <div class="mt-4">
                <label for="nominal_bayar" class="block text-sm font-medium text-gray-700">
                    Nominal Bayar
                </label>
                <input wire:model.live="nominal_bayar" type="number" id="nominal_bayar" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Masukkan nominal bayar">
            </div>

            <div class="mt-2">
                <label class="block text-sm font-medium text-gray-700">Kembalian</label>
                <p class="mt-1 text-lg font-semibold">

                </p>
            </div>

            <button wire:click="prosesTransaksi" class="block w-full py-2 bg-gray-700 text-white font-bold rounded-md hover:bg-gray-800">
                Proses Transaksi
            </button>
        </div>
    </div>
</div>