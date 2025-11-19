<?php

namespace App\Livewire;


use App\Models\Product;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateTransaction extends Component
{
    public $customer_id = null;
    public $transaction_date;
    public $items = [];
    public $discount = 0;
    public $additional_fee = 0;
    public $note = '';
    public $admin_note = '';
    public $status = 'pending';
    public $payment_method = '';
    public $nominal_bayar = 0;

    // Untuk pencarian
    public $search_customer = '';
    public $search_product = '';
    public $selected_product_id = null;
    public $quantity = 1;
    public $show_products = false;

    protected $rules = [
        'transaction_date' => 'required|date',
        'items' => 'required|array|min:1',
        'discount' => 'numeric|min:0',
        'additional_fee' => 'numeric|min:0',
        'status' => 'required|in:pending,paid,cancelled',
    ];

    protected $messages = [
        'transaction_date.required' => 'Tanggal transaksi harus diisi',
        'items.required' => 'Minimal harus ada 1 produk',
        'items.min' => 'Minimal harus ada 1 produk',
    ];

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->discount = 0;
        $this->additional_fee = 0;
        $this->nominal_bayar = 0;
    }

    // Auto-cast nilai saat input berubah
    public function updatedDiscount($value)
    {
        $this->discount = is_numeric($value) ? (float) $value : 0;
    }

    public function updatedAdditionalFee($value)
    {
        $this->additional_fee = is_numeric($value) ? (float) $value : 0;
    }

    public function updatedNominalBayar($value)
    {
        $this->nominal_bayar = is_numeric($value) ? (float) $value : 0;
    }

    // Method untuk mendapatkan daftar customer
    public function getCustomers()
    {
        if (empty($this->search_customer)) {
            return collect([]);
        }

        return Customer::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search_customer . '%')
                ->orWhere('email', 'like', '%' . $this->search_customer . '%');
        })
            ->limit(10)
            ->get();
    }

    // Method untuk mendapatkan daftar produk
    public function getProducts()
    {
        // Jika user mengetik sesuatu, cari berdasarkan input
        if (!empty($this->search_product)) {
            return Product::where('name', 'like', '%' . $this->search_product . '%')
                ->limit(10)
                ->get();
        }

        // Jika input kosong tapi di-focus, tampilkan 5 produk pertama
        if ($this->show_products && empty($this->search_product)) {
            return Product::orderBy('name', 'asc')
                ->limit(5)
                ->get();
        }

        // Default: tidak tampilkan apa-apa
        return collect([]);
    }

    // Method ketika input pencarian produk di-focus
    public function focusProduct()
    {
        $this->show_products = true;
    }

    // Method untuk hide dropdown produk
    public function hideProducts()
    {
        $this->show_products = false;
    }

    // Method untuk pilih produk (langsung tambah ke cart)
    public function pilihProduk($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Produk tidak ditemukan'
            ]);
            return;
        }

        // Cek apakah produk sudah ada di items
        $existing_index = collect($this->items)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existing_index !== false) {
            // Update quantity jika sudah ada
            $this->items[$existing_index]['quantity'] += 1;
            $this->items[$existing_index]['subtotal'] =
                $this->items[$existing_index]['quantity'] * $this->items[$existing_index]['price'];
        } else {
            // Tambah item baru
            $this->items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
                'price' => (float) $product->price,
                'subtotal' => (float) $product->price,
            ];
        }

        // Reset form dan hide dropdown
        $this->search_product = '';
        $this->show_products = false;
    }

    // Method untuk tambah item manual (jika masih dipakai)
    public function addItem()
    {
        if (!$this->selected_product_id || $this->quantity < 1) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Pilih produk dan masukkan quantity yang valid'
            ]);
            return;
        }

        $product = Product::find($this->selected_product_id);

        if (!$product) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Produk tidak ditemukan'
            ]);
            return;
        }

        // Cek apakah produk sudah ada di items
        $existing_index = collect($this->items)->search(function ($item) {
            return $item['product_id'] == $this->selected_product_id;
        });

        if ($existing_index !== false) {
            // Update quantity jika sudah ada
            $this->items[$existing_index]['quantity'] += $this->quantity;
            $this->items[$existing_index]['subtotal'] =
                $this->items[$existing_index]['quantity'] * $this->items[$existing_index]['price'];
        } else {
            // Tambah item baru
            $this->items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $this->quantity,
                'price' => (float) $product->price,
                'subtotal' => (float) $product->price * $this->quantity,
            ];
        }

        // Reset form
        $this->selected_product_id = null;
        $this->quantity = 1;
        $this->search_product = '';
    }

    // Method untuk hapus item
    public function removeItem($index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Produk berhasil dihapus'
            ]);
        }
    }

    // Method untuk update quantity manual (jika pakai input text)
    public function updateQuantity($index, $quantity)
    {
        $qty = (int) $quantity;

        if ($qty < 1) {
            $this->removeItem($index);
            return;
        }

        if (isset($this->items[$index])) {
            $this->items[$index]['quantity'] = $qty;
            $this->items[$index]['subtotal'] = $this->items[$index]['price'] * $qty;
        }
    }

    // Method untuk tambah quantity (tombol +)
    public function tambahQuantity($index)
    {
        if (isset($this->items[$index])) {
            $this->items[$index]['quantity']++;
            $this->items[$index]['subtotal'] =
                $this->items[$index]['price'] * $this->items[$index]['quantity'];
        }
    }

    // Method untuk kurang quantity (tombol -)
    public function kurangQuantity($index)
    {
        if (isset($this->items[$index])) {
            if ($this->items[$index]['quantity'] > 1) {
                $this->items[$index]['quantity']--;
                $this->items[$index]['subtotal'] =
                    $this->items[$index]['price'] * $this->items[$index]['quantity'];
            } else {
                // Jika quantity 1 dan dikurang, hapus item
                $this->removeItem($index);
            }
        }
    }

    // Method untuk menghitung total
    public function getTotal()
    {
        return (float) collect($this->items)->sum('subtotal');
    }

    // Method untuk menghitung grand total
    public function getGrandTotal()
    {
        $total = $this->getTotal();
        $discount = (float) ($this->discount ?? 0);
        $additionalFee = (float) ($this->additional_fee ?? 0);

        return $total - $discount + $additionalFee;
    }

    // Method untuk menghitung kembalian
    public function getKembalian()
    {
        $bayar = (float) ($this->nominal_bayar ?? 0);
        $grandTotal = $this->getGrandTotal();

        return max(0, $bayar - $grandTotal);
    }

    // Method untuk simpan transaksi
    public function save()
    {
        // Validasi
        $this->validate();

        // Validasi tambahan
        if (empty($this->items)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Belum ada produk yang dipilih'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Buat transaksi
            $transaction = Transaction::create([
                'customer_id' => $this->customer_id ?: null,
                'transaction_date' => $this->transaction_date,
                'total' => $this->getTotal(),
                'discount' => (float) $this->discount,
                'additional_fee' => (float) $this->additional_fee,
                'grand_total' => $this->getGrandTotal(),
                'status' => $this->status,
                'payment_method' => $this->payment_method,
                'note' => $this->note,
                'admin_note' => $this->admin_note,
            ]);

            // Buat transaction items
            foreach ($this->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => (float) $item['price'],
                    'subtotal' => (float) $item['subtotal'],
                ]);

                // Optional: Kurangi stok produk
                // Uncomment jika ingin auto kurangi stok

                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            // Flash message success
            session()->flash('success', 'Transaksi berhasil dibuat!');

            // Redirect ke halaman index
            return redirect()->route('filament.admin.resources.transactions.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating transaction: ' . $e->getMessage());

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.create-transaction');
    }
}
