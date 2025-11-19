<div>
    <form wire:submit.prevent="save">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-8">

                <x-filament::section class="shadow-lg border-t-4 border-primary-500 rounded-xl">
                    <div class="relative">
                        <x-filament::input type="text" wire:model.live.debounce.300ms="search_product" wire:focus="focusProduct" placeholder="🔍 Cari produk berdasarkan nama, SKU, atau barcode..." autocomplete="off" class="text-lg py-0" />

                        @if($this->getProducts()->count() > 0)
                        <div class="absolute w-full mt-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
                            @foreach($this->getProducts() as $product)
                            <div wire:click="pilihProduk({{ $product->id }})" class="px-5 py-3 hover:bg-primary-50 dark:hover:bg-gray-700/50 cursor-pointer transition duration-150 border-b dark:border-gray-800 last:border-b-0">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1 min-w-0 pr-4">
                                        <div class="font-bold text-gray-900 dark:text-white truncate">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku ?? 'N/A' }}</div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="font-extrabold text-lg text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-amber-600 dark:text-amber-400">
                                            Stok: {{ $product->stock ?? '0' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="">
                            @endforeach
                        </div>
                        @endif
                    </div>
                </x-filament::section>

                <x-filament::section class="rounded-xl">
                    <x-slot name="heading">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold flex items-center gap-2">
                                🛒 Daftar Belanja
                            </h2>
                            <x-filament::badge color="primary">{{ count($items) }} Item</x-filament::badge>
                        </div>
                    </x-slot>

                    @if(count($items) > 0)
                    <div class="space-y-4" style="max-height: 450px; overflow-y: auto; padding-right: 1rem;">
                        @foreach($items as $index => $item)
                        <div class="p-4 bg-white dark:bg-gray-800 shadow-md rounded-lg transition duration-150 border dark:border-gray-700">
                            <div class="flex items-center gap-4">

                                <div class="flex-shrink-0 w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-full flex items-center justify-center">
                                    <x-filament::icon icon="heroicon-o-archive-box" class="w-6 h-6" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="font-extrabold truncate text-lg">{{ $item['product_name'] }}</div>
                                    <div class="text-sm text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                </div>

                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <x-filament::button wire:click="kurangQuantity({{ $index }})" color="gray" size="sm" outlined :disabled="$item['quantity'] <= 1" class="w-8 h-8 p-0">
                                        <x-filament::icon icon="heroicon-m-minus" class="w-4 h-4" />
                                    </x-filament::button>

                                    <span class="w-10 text-center font-extrabold text-base dark:text-white">{{ $item['quantity'] }}</span>

                                    <x-filament::button wire:click="tambahQuantity({{ $index }})" color="success" size="sm" class="w-8 h-8 p-0">
                                        <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                                    </x-filament::button>
                                </div>

                                <div class="text-right font-extrabold text-xl text-primary-600 dark:text-primary-400 w-32 flex-shrink-0">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </div>

                                <x-filament::button wire:click="removeItem({{ $index }})" color="danger" size="sm" icon="heroicon-o-x-mark" outlined class="p-2 h-10 w-10 flex-shrink-0" />
                            </div>
                        </div>
                        <hr class="my-2 border-gray-200 dark:border-gray-700" />
                        @endforeach
                    </div>
                    @else
                    <div class="py-16 text-center text-gray-400 dark:text-gray-500">
                        <x-filament::icon icon="heroicon-o-shopping-bag" class="w-16 h-16 mx-auto mb-4" />
                        <p class="text-lg font-semibold">Keranjang Belanja Kosong</p>
                        <p class="text-sm">Silakan cari dan tambahkan produk untuk memulai transaksi.</p>
                    </div>
                    @endif
                </x-filament::section>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-20 space-y-8">

                    <x-filament::section class="rounded-xl">
                        <x-slot name="heading">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                👤 Detail Customer & Transaksi
                            </h3>
                        </x-slot>

                        <div class="space-y-4">
                            <div class="relative">
                                <x-filament::input type="text" wire:model.live.debounce.250ms="search_customer" placeholder="Cari atau Tambah Customer..." />

                                @if($search_customer && $this->getCustomers()->count() > 0)
                                <div class="absolute w-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-20 max-h-48 overflow-y-auto">
                                    @foreach($this->getCustomers() as $customer)
                                    <div wire:click="$set('customer_id', {{ $customer->id }}); $set('search_customer', '{{ $customer->name }}')" class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-gray-700/50 cursor-pointer border-b dark:border-gray-800 last:border-b-0">
                                        <div class="font-semibold text-sm">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($customer_id)
                                <div class="mt-2 flex items-center justify-between bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 px-3 py-2 rounded-lg text-sm font-medium border border-success-200 dark:border-success-900">
                                    <span>Customer Terpilih: **{{ $this->getCustomers()->find($customer_id)?->name }}**</span>
                                    <button type="button" wire:click="$set('customer_id', null); $set('search_customer', '')" class="text-danger-600 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-200">
                                        <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                                @endif
                            </div>

                            <x-filament::input type="date" wire:model="transaction_date" />
                        </div>
                    </x-filament::section>

                    <x-filament::section class="rounded-xl">
                        <x-slot name="heading">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                💰 Ringkasan Pembayaran
                            </h3>
                        </x-slot>

                        <div class="space-y-4">
                            <div class="flex justify-between text-base">
                                <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                                <span class="font-extrabold dark:text-white">Rp {{ number_format($this->getTotal(), 0, ',', '.') }}</span>
                            </div>

                            <x-filament::input type="number" wire:model.live.debounce.500ms="discount" min="0" placeholder="Diskon (Masukkan nilai Rp)" class="text-right" />

                            <div class="flex justify-between text-base">
                                <span class="text-gray-600 dark:text-gray-300">Diskon</span>
                                <span class="font-extrabold text-danger-600 dark:text-danger-400">- Rp {{ number_format((float)$discount ?: 0, 0, ',', '.') }}</span>
                            </div>

                            <x-filament::input type="number" wire:model.live.debounce.500ms="additional_fee" min="0" placeholder="Biaya Tambahan (e.g. Ongkir)" class="text-right" />

                            <div class="flex justify-between text-base">
                                <span class="text-gray-600 dark:text-gray-300">Biaya Tambahan</span>
                                <span class="font-extrabold text-success-600 dark:text-success-400">+ Rp {{ number_format($additional_fee, 0, ',', '.') }}</span>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex justify-between text-2xl font-extrabold text-primary-600 dark:text-primary-400">
                                    <span>TOTAL AKHIR</span>
                                    <span>Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    <x-filament::section class="rounded-xl">
                        <x-slot name="heading">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                💳 Pembayaran
                            </h3>
                        </x-slot>

                        <div class="space-y-4">
                            <select wire:model="payment_method" class="w-full text-base border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="cash">Cash (Tunai)</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="card">Kartu Kredit/Debit</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>

                            <x-filament::input type="number" wire:model.live.debounce.300ms="nominal_bayar" placeholder="Nominal Uang Bayar" class="text-lg py-3 text-right" />

                            @if($nominal_bayar > $this->getGrandTotal())
                            <div class="bg-primary-50 dark:bg-primary-900/30 p-4 rounded-xl border border-primary-200 dark:border-primary-800">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-semibold text-primary-700 dark:text-primary-300">Kembalian</span>
                                    <span class="text-2xl font-extrabold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format(max(0, $nominal_bayar - $this->getGrandTotal()), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            @elseif($nominal_bayar > 0 && $nominal_bayar < $this->getGrandTotal())
                                <div class="bg-danger-50 dark:bg-danger-900/30 p-4 rounded-xl border border-danger-200 dark:border-danger-800">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-danger-700 dark:text-danger-300">Kurang Bayar</span>
                                        <span class="text-lg font-bold text-danger-600 dark:text-danger-400">
                                            Rp {{ number_format($this->getGrandTotal() - $nominal_bayar, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                @endif

                                <select wire:model="status" class="w-full text-base border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="pending">Status: Pending</option>
                                    <option value="paid">Status: Paid (Lunas)</option>
                                    <option value="cancelled">Status: Cancelled</option>
                                </select>

                                <textarea wire:model="note" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Catatan Tambahan untuk Transaksi..."></textarea>
                        </div>
                    </x-filament::section>

                    <div class="space-y-3">
                        <x-filament::button type="submit" color="success" size="xl" class="w-full justify-center shadow-lg hover:shadow-xl transition duration-200" :disabled="count($items) === 0 || $this->getGrandTotal() > $nominal_bayar && $status === 'paid'">
                            <span class="text-lg font-bold">✓ Proses Transaksi</span>
                        </x-filament::button>

                        <x-filament::button href="{{ route('filament.admin.resources.transactions.index') }}" color="gray" tag="a" class="w-full justify-center" outlined>
                            <span class="font-semibold">Batal & Kembali</span>
                        </x-filament::button>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>