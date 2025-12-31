<div>
    <form wire:submit.prevent="save">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-8">
            <!-- Main Content Area -->
            <div class="lg:col-span-3 space-y-4 lg:space-y-8">

                <!-- Product Search -->
                <x-filament::section class="shadow-lg border-t-4 border-primary-500 rounded-xl">
                    <div class="relative">
                        <x-filament::input type="text" wire:model.live.debounce.300ms="search_product" wire:focus="focusProduct" placeholder="🔍 Cari produk..." autocomplete="off" class="text-base lg:text-lg py-0 lg:py-0" />

                        @if($this->getProducts()->count() > 0)
                        <div class="absolute w-full mt-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 max-h-60 lg:max-h-96 overflow-y-auto">
                            @foreach($this->getProducts() as $product)
                            <div wire:click="pilihProduk({{ $product->id }})" class="px-3 lg:px-5 py-2 lg:py-3 hover:bg-primary-50 dark:hover:bg-gray-700/50 cursor-pointer transition duration-150 border-b dark:border-gray-800 last:border-b-0">
                                <div class="flex justify-between items-center gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-sm lg:text-base text-gray-900 dark:text-white truncate">
                                            {{ $product->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            SKU: {{ $product->sku ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="font-extrabold text-sm lg:text-lg text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs lg:text-sm text-amber-600 dark:text-amber-400">
                                            Stok: {{ $product->stock ?? '0' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </x-filament::section>

                <!-- Shopping Cart -->
                <x-filament::section class="rounded-xl">
                    <x-slot name="heading">
                        <div class="flex justify-between items-center gap-2">
                            <h2 class="text-sm lg:text-xl font-bold flex items-center gap-1 lg:gap-2">
                                <span class="text-base lg:text-xl">🛒</span>
                                <span class="hidden sm:inline">Daftar Belanja</span>
                                <span class="sm:hidden">Keranjang</span>
                            </h2>
                            <x-filament::badge color="primary" class="text-xs">{{ count($items) }}</x-filament::badge>
                        </div>
                    </x-slot>

                    @if(count($items) > 0)
                    <div class="space-y-2 max-h-[350px] lg:max-h-[450px] overflow-y-auto pr-1">
                        @foreach($items as $index => $item)
                        <div class="p-2 lg:p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 transition">

                            <!-- Mobile & Tablet Layout -->
                            <div class="lg:hidden">
                                <!-- Row 1: Product Name & Delete -->
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-xs leading-tight line-clamp-2 dark:text-white">
                                            {{ $item['product_name'] }}
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            @Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <button wire:click="removeItem({{ $index }})" type="button" class="flex-shrink-0 p-1 text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 rounded transition">
                                        <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4" />
                                    </button>
                                </div>

                                <!-- Row 2: Quantity Controls & Subtotal -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <button wire:click="kurangQuantity({{ $index }})" type="button" @if($item['quantity'] <=1) disabled @endif class="w-6 h-6 flex items-center justify-center bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                            <x-filament::icon icon="heroicon-m-minus" class="w-3 h-3" />
                                        </button>

                                        <span class="w-7 text-center text-sm font-bold dark:text-white">
                                            {{ $item['quantity'] }}
                                        </span>

                                        <!-- ✅ Gunakan class Filament -->
                                        <button wire:click="tambahQuantity({{ $index }})" type="button" class="w-6 h-6 flex items-center justify-center bg-success-600 dark:bg-success-500 text-white rounded hover:bg-success-700 dark:hover:bg-success-600 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="font-bold text-sm text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop Layout -->
                            <div class="hidden lg:flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg flex items-center justify-center">
                                    <x-filament::icon icon="heroicon-o-cube" class="w-5 h-5" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-sm truncate dark:text-white">{{ $item['product_name'] }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>

                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button wire:click="kurangQuantity({{ $index }})" type="button" @if($item['quantity'] <=1) disabled @endif class="w-7 h-7 flex items-center justify-center bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 transition">
                                        <x-filament::icon icon="heroicon-m-minus" class="w-3.5 h-3.5" />
                                    </button>

                                    <span class="w-8 text-center text-sm font-bold dark:text-white">{{ $item['quantity'] }}</span>

                                    <button wire:click="tambahQuantity({{ $index }})" type="button" class="w-7 h-7 flex items-center justify-center bg-success-500 text-white rounded hover:bg-success-600 transition">
                                        <x-filament::icon icon="heroicon-m-plus" class="w-3.5 h-3.5" />
                                    </button>
                                </div>

                                <div class="text-right font-bold text-base text-primary-600 dark:text-primary-400 w-28 flex-shrink-0">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </div>

                                <button wire:click="removeItem({{ $index }})" type="button" class="p-2 text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 rounded transition flex-shrink-0">
                                    <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 lg:py-12 text-center text-gray-400 dark:text-gray-500">
                        <x-filament::icon icon="heroicon-o-shopping-bag" class="w-10 h-10 lg:w-14 lg:h-14 mx-auto mb-2 lg:mb-3 opacity-50" />
                        <p class="text-sm lg:text-base font-semibold">Keranjang Kosong</p>
                        <p class="text-xs lg:text-sm mt-1">Tambahkan produk untuk memulai</p>
                    </div>
                    @endif
                </x-filament::section>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-20 space-y-4 lg:space-y-8">

                    <!-- Customer Details -->
                    <x-filament::section class="rounded-xl">
                        <x-slot name="heading">
                            <h3 class="text-sm lg:text-lg font-bold flex items-center gap-2">
                                👤 Detail Customer
                            </h3>
                        </x-slot>

                        <div class="space-y-3 lg:space-y-4">
                            <div class="relative">
                                <x-filament::input type="text" wire:model.live.debounce.250ms="search_customer" placeholder="Cari Customer..." class="text-sm lg:text-base" />

                                @if($search_customer && $this->getCustomers()->count() > 0)
                                <div class="absolute w-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-20 max-h-40 lg:max-h-48 overflow-y-auto">
                                    @foreach($this->getCustomers() as $customer)
                                    <div wire:click="$set('customer_id', {{ $customer->id }}); $set('search_customer', '{{ $customer->name }}')" class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-gray-700/50 cursor-pointer border-b dark:border-gray-800 last:border-b-0">
                                        <div class="font-semibold text-xs lg:text-sm">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($customer_id)
                                <div class="mt-2 flex items-center justify-between bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 px-3 py-2 rounded-lg text-xs lg:text-sm font-medium border border-success-200 dark:border-success-900">
                                    <span class="truncate">{{ $this->getCustomers()->find($customer_id)?->name }}</span>
                                    <button type="button" wire:click="$set('customer_id', null); $set('search_customer', '')" class="text-danger-600 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-200 ml-2">
                                        <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                                @endif
                            </div>

                            <x-filament::input type="date" wire:model="transaction_date" class="text-sm lg:text-base" />
                        </div>
                    </x-filament::section>

                    <!-- Payment Summary -->
                    <x-filament::section class="rounded-xl">
                        <x-slot name="heading">
                            <h3 class="text-sm lg:text-lg font-bold flex items-center gap-2">
                                💰 Ringkasan
                            </h3>
                        </x-slot>

                        <div class="space-y-3 lg:space-y-4">
                            <div class="flex justify-between text-sm lg:text-base">
                                <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                                <span class="font-bold dark:text-white">Rp {{ number_format($this->getTotal(), 0, ',', '.') }}</span>
                            </div>



                            <div class="flex justify-between text-sm lg:text-base">
                                <span class="text-gray-600 dark:text-gray-300">Diskon</span>
                                <span class="font-bold text-danger-600 dark:text-danger-400"><x-filament::input type="number" wire:model.live.debounce.500ms="discount" min="0" placeholder="Diskon (Rp)" class="text-right text-sm lg:text-base" /></span>
                            </div>



                            <div class="flex justify-between text-sm lg:text-base">
                                <span class="text-gray-600 dark:text-gray-300">Biaya Tambahan</span>
                                <span class="font-bold text-success-600 dark:text-success-400"> <x-filament::input type="number" wire:model.live.debounce.500ms="additional_fee" min="0" placeholder="Biaya Tambahan" class="text-right text-sm lg:text-base" /></span>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 lg:pt-4">
                                <div class="flex justify-between text-lg lg:text-2xl font-extrabold text-primary-600 dark:text-primary-400">
                                    <span>TOTAL</span>
                                    <span>Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    <!-- Payment Method -->
                    <x-filament::section class="rounded-xl">
                        <x-slot name="heading">
                            <h3 class="text-sm lg:text-lg font-bold flex items-center gap-2">
                                💳 Pembayaran
                            </h3>
                        </x-slot>

                        <div class="space-y-3 lg:space-y-4">
                            <select wire:model="payment_method" class="w-full text-sm lg:text-base border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Pilih Metode</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="card">Kartu</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>

                            <x-filament::input type="number" wire:model.live.debounce.300ms="nominal_bayar" placeholder="Nominal Bayar" class="text-base lg:text-lg py-2 lg:py-3 text-right" />

                            @if($nominal_bayar > $this->getGrandTotal())
                            <div class="bg-primary-50 dark:bg-primary-900/30 p-3 lg:p-4 rounded-xl border border-primary-200 dark:border-primary-800">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm lg:text-base font-semibold text-primary-700 dark:text-primary-300">Kembalian</span>
                                    <span class="text-lg lg:text-2xl font-extrabold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format(max(0, $nominal_bayar - $this->getGrandTotal()), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            @elseif($nominal_bayar > 0 && $nominal_bayar < $this->getGrandTotal())
                                <div class="bg-danger-50 dark:bg-danger-900/30 p-3 lg:p-4 rounded-xl border border-danger-200 dark:border-danger-800">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs lg:text-sm font-semibold text-danger-700 dark:text-danger-300">Kurang Bayar</span>
                                        <span class="text-base lg:text-lg font-bold text-danger-600 dark:text-danger-400">
                                            Rp {{ number_format($this->getGrandTotal() - $nominal_bayar, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                @endif

                                <select wire:model="status" class="w-full text-sm lg:text-base border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid (Lunas)</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>

                                <textarea wire:model="note" rows="2" class="w-full px-3 py-2 text-sm lg:text-base border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Catatan..."></textarea>
                        </div>
                    </x-filament::section>

                    <!-- Action Buttons -->
                    <div class="space-y-2 lg:space-y-3">
                        <x-filament::button type="submit" color="success" size="lg" class="w-full justify-center shadow-lg hover:shadow-xl transition duration-200" :disabled="count($items) === 0 || $this->getGrandTotal() > $nominal_bayar && $status === 'paid'">
                            <span class="text-base lg:text-lg font-bold">✓ Proses Transaksi</span>
                        </x-filament::button>

                        <x-filament::button href="{{ route('filament.admin.resources.transactions.index') }}" color="gray" tag="a" class="w-full justify-center" outlined>
                            <span class="text-sm lg:text-base font-semibold">Batal</span>
                        </x-filament::button>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>