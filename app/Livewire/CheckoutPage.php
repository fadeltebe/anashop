<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Transaction;
use App\Services\CartService;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutPage extends Component
{
    public $name;
    public $phone;
    public $address;
    public $payment_method = '';

    protected $rules = [
        'name' => 'required|string|min:3',
        'phone' => 'required|string|min:8',
        'address' => 'required|string|min:5',
    ];

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

            if (! $customer) {
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

            // 🔹 Cek transaksi aktif
            $existingTransaction = Transaction::where('customer_id', $customer->id)
                ->whereIn('status', ['pending', 'paid'])
                ->latest()
                ->first();

            if ($existingTransaction && $existingTransaction->status === 'pending') {
                $grandTotalTambah = 0;

                foreach ($cart->items as $item) {
                    $product = $item->product;

                    // 🔹 Cek stok produk
                    if ($product->stock < $item->quantity) {
                        DB::rollBack();
                        session()->flash('error', "Stok untuk {$product->name} tidak mencukupi.");
                        return redirect()->route('cart.index');
                    }

                    // 🔹 Cek apakah produk sudah ada di transaksi
                    $existingItem = TransactionItem::where('transaction_id', $existingTransaction->id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($existingItem) {
                        $newQty = $existingItem->quantity + $item->quantity;

                        if ($product->stock < $newQty) {
                            DB::rollBack();
                            session()->flash('error', "Stok untuk {$product->name} tidak mencukupi untuk menambah {$item->quantity} pcs lagi.");
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
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->price * $item->quantity,
                        ]);
                    }

                    $product->decrement('stock', $item->quantity);
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

            if ($existingTransaction && $existingTransaction->status === 'paid') {
                DB::rollBack();
                session()->flash('error', 'Pesanan Anda sebelumnya sedang diproses. Harap selesaikan dulu sebelum membuat pesanan baru.');
                return redirect()->route('cart.index');
            }

            // 🔹 Buat transaksi baru
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'transaction_date' => now(),
                'total' => $cart->items->sum(fn($i) => $i->price * $i->quantity),
                'discount' => 0,
                'additional_fee' => 0,
                'grand_total' => $cart->items->sum(fn($i) => $i->price * $i->quantity),
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;

                if ($product->stock < $item->quantity) {
                    DB::rollBack();
                    session()->flash('error', "Stok untuk {$product->name} tidak mencukupi.");
                    return redirect()->route('cart.index');
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $cartService->clearCart();
            DB::commit();

            session()->flash('success', 'Checkout berhasil! Pesanan Anda sedang diproses.');
            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal melakukan checkout: ' . $e->getMessage());
            return redirect()->route('cart.index');
        }
    }



    public function render()
    {
        return view('livewire.checkout-page');
    }
}
