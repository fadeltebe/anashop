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
    public $payment_method = 'cod';

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

            if ($cart->items->isEmpty()) {
                session()->flash('error', 'Keranjang Anda kosong.');
                return;
            }

            // 🔹 Cek apakah sudah ada customer dengan nomor HP ini
            $customer = Customer::where('phone', $this->phone)->first();

            if (! $customer) {
                // Dapatkan kode terakhir (misal: CUST0005 → ambil angka 5)
                $lastCustomer = Customer::orderBy('id', 'desc')->first();
                $nextNumber = $lastCustomer
                    ? ((int) filter_var($lastCustomer->customer_code, FILTER_SANITIZE_NUMBER_INT) + 1)
                    : 1;

                // Buat kode baru seperti CUST0006
                $newCode = 'CUST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // 🔹 Cek apakah user sedang login
                $userId = Auth::check() ? Auth::id() : null;

                // Buat customer baru
                $customer = Customer::create([
                    'customer_code' => $newCode,
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'user_id' => $userId,
                ]);
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

            // 🔹 Simpan item ke tabel transaction_items
            foreach ($cart->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ]);
            }

            // 🔹 Kosongkan keranjang
            $cartService->clearCart();

            DB::commit();

            session()->flash('success', 'Checkout berhasil! Pesanan Anda sedang diproses.');
            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal melakukan checkout: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.checkout-page');
    }
}
