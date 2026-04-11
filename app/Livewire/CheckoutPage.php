<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Transaction;
use App\Services\CartService;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutPage extends Component
{
    public $payment_method = '';
    public $cart;
    public $total = 0;

    protected $rules = [
        'payment_method' => 'required|string|in:qris,transfer,e_wallet,cod',
    ];

    public function mount()
    {
        // Ambil cart beserta items
        $cartService = app(CartService::class);
        $this->cart = $cartService->getCartWithItems();

        // Hitung total
        $this->total = $this->cart->items->sum(fn($item) => $item->price * $item->quantity);

        // Jika cart kosong, redirect ke halaman cart
        if ($this->cart->items->isEmpty()) {
            session()->flash('error', 'Keranjang Anda kosong.');
            return redirect()->route('cart.index');
        }
    }

    public function submit()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $cartService = app(CartService::class);
            $cart = $cartService->getCartWithItems();

            // 🔹 Jika keranjang kosong → kembalikan ke halaman cart
            if ($cart->items->isEmpty()) {
                DB::rollBack();
                session()->flash('error', 'Keranjang Anda kosong.');
                return redirect()->route('cart.index');
            }

            // 🔹 Cari customer berdasarkan nomor HP
            $customer = Customer::where('phone', $this->phone)->first();

            if (!$customer) {
                $lastCustomer = Customer::orderBy('id', 'desc')->first();
                $nextNumber = $lastCustomer
                    ? ((int) filter_var($lastCustomer->customer_code, FILTER_SANITIZE_NUMBER_INT) + 1)
                    : 1;

                $newCode = 'CUST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $userId = Auth::check() ? Auth::id() : null;

                $customer = Customer::create([
                    'customer_code' => $newCode,
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'user_id' => $userId,
                ]);
            }

            // 🔹 Cek transaksi aktif (hanya untuk merge jika Pending)
            $existingTransaction = Transaction::where('customer_id', $customer->id)
                ->where('status', 'Pending')  // Fokus ke Pending saja
                ->latest()
                ->first();

            if ($existingTransaction) {
                $grandTotalTambah = 0;

                foreach ($cart->items as $item) {
                    // 🔹 Validasi stok (tetap lakukan, tapi jangan kurangi stok di sini)
                    if ($item->available_stock < $item->quantity) {
                        DB::rollBack();
                        session()->flash('error', "Stok untuk {$item->product_name} (variant: {$item->variant_name}) tidak mencukupi.");
                        return redirect()->route('cart.index');
                    }

                    // 🔹 Cek apakah produk sudah ada di transaksi
                    $existingItem = TransactionItem::where('transaction_id', $existingTransaction->id)
                        ->where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->first();

                    if ($existingItem) {
                        $newQty = $existingItem->quantity + $item->quantity;

                        if ($item->available_stock < $newQty) {
                            DB::rollBack();
                            session()->flash('error', "Stok untuk {$item->product_name} (variant: {$item->variant_name}) tidak mencukupi untuk menambah {$item->quantity} pcs lagi.");
                            return redirect()->route('cart.index');
                        }

                        $existingItem->update([
                            'quantity' => $newQty,
                            'subtotal' => $newQty * $item->price,
                        ]);
                    } else {
                        TransactionItem::create([
                            'transaction_id' => $existingTransaction->id,
                            'product_id' => $item->product_id,
                            'variant_id' => $item->variant_id,
                            'product_name' => $item->product->name,
                            'variant_name' => $item->variant?->variant_name,  // Perbaiki: variant_name, bukan name
                            'sku' => $item->variant?->sku,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->price * $item->quantity,
                        ]);
                    }

                    // ❌ HAPUS: Jangan decrement stok di sini (akan dilakukan saat payment completed)
                    $grandTotalTambah += $item->price * $item->quantity;
                }

                $existingTransaction->update([
                    'grand_total' => $existingTransaction->grand_total + $grandTotalTambah,
                ]);

                DB::commit();
                $cartService->clearCart();

                session()->flash('success', 'Produk baru ditambahkan ke pesanan lama Anda.');
                return redirect()->route('home');
            }

            // 🔹 Buat transaksi baru
            $transaction = Transaction::create([
                'transaction_code' => 'TRX' . strtoupper(uniqid()),
                'customer_id' => $customer->id,
                'transaction_date' => now(),
                'total' => $cart->items->sum(fn($i) => $i->price * $i->quantity),
                'discount' => 0,
                'additional_fee' => 0,
                'grand_total' => $cart->items->sum(fn($i) => $i->price * $i->quantity),
                'status' => 'Pending',
                'payment_method' => $this->payment_method,
                'payment_status' => 'unpaid',  // Pastikan default unpaid
            ]);

            foreach ($cart->items as $item) {
                // 🔹 Validasi stok (tetap lakukan)
                if ($item->available_stock < $item->quantity) {
                    DB::rollBack();
                    session()->flash('error', "Stok untuk {$item->product_name} (variant: {$item->variant_name}) tidak mencukupi.");
                    return redirect()->route('cart.index');
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant?->variant_name,  // Perbaiki
                    'sku' => $item->variant?->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ]);

                // ❌ HAPUS: Jangan decrement stok di sini
            }

            $cartService->clearCart();
            DB::commit();

            Log::info('Checkout berhasil', ['transaction_id' => $transaction->id, 'customer_id' => $customer->id]);

            session()->flash('success', 'Checkout berhasil! Silakan lakukan pembayaran.');
            return redirect()->route('home');  // Atau ke halaman pembayaran
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage(), [
                'customer_phone' => $this->phone,
                'cart_items' => $cart->items->pluck('id')->toArray(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Gagal melakukan checkout: ' . $e->getMessage());
            return redirect()->route('cart.index');
        }
    }

    // ❌ HAPUS: Fungsi decrementVariantStock tidak diperlukan lagi di sini

    public function render()
    {
        return view('livewire.checkout-page');
    }
}
