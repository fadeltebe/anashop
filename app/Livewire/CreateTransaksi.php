<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;

use Illuminate\Support\Facades\DB;
use App\Filament\Resources\Transactions\TransactionResource;

class CreateTransaksi extends Component
{
    public $produkTerpilih = [];
    public $search = '';
    public $total = 0;
    public $discount = 0;
    public $additional_fee = 0;
    public $grand_total = 0;
    public $note;
    public $customer_id = null;
    public $status = 'pending';
    public $tampil = false;

    public function pilihProduk(Product $product)
    {
        $index = array_search($product->id, array_column($this->produkTerpilih, 'id'));

        if ($index !== false) {
            $this->produkTerpilih[$index]['quantity']++;
            $this->produkTerpilih[$index]['subtotal'] = $this->produkTerpilih[$index]['quantity'] * $product->price;
        } else {
            $this->produkTerpilih[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
                'image' => $product->image ?? null,
            ];
        }

        $this->hitungTotal();
        $this->search = '';
        $this->tampil = false;
    }

    public function tambahProduk($produkId)
    {
        $index = array_search($produkId, array_column($this->produkTerpilih, 'id'));
        if ($index !== false) {
            $this->produkTerpilih[$index]['quantity']++;
            $this->produkTerpilih[$index]['subtotal'] =
                $this->produkTerpilih[$index]['quantity'] * $this->produkTerpilih[$index]['price'];
            $this->hitungTotal();
        }
    }

    public function kurangProduk($produkId)
    {
        $index = array_search($produkId, array_column($this->produkTerpilih, 'id'));
        if ($index !== false) {
            if ($this->produkTerpilih[$index]['quantity'] > 1) {
                $this->produkTerpilih[$index]['quantity']--;
                $this->produkTerpilih[$index]['subtotal'] =
                    $this->produkTerpilih[$index]['quantity'] * $this->produkTerpilih[$index]['price'];
            } else {
                unset($this->produkTerpilih[$index]);
                $this->produkTerpilih = array_values($this->produkTerpilih);
            }
            $this->hitungTotal();
        }
    }

    public function hitungTotal()
    {
        $this->total = array_sum(array_column($this->produkTerpilih, 'subtotal'));
        $this->grand_total = $this->total - $this->discount + $this->additional_fee;
    }

    public function updatedDiscount()
    {
        $this->hitungTotal();
    }

    public function updatedAdditionalFee()
    {
        $this->hitungTotal();
    }

    public function prosesTransaksi()
    {
        $this->validate([
            'produkTerpilih' => 'required|array|min:1',
            'discount' => 'nullable|numeric|min:0',
            'additional_fee' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'customer_id' => $this->customer_id,
                'transaction_date' => now(),
                'total' => $this->total,
                'discount' => $this->discount,
                'additional_fee' => $this->additional_fee,
                'grand_total' => $this->grand_total,
                'status' => $this->status,
                'note' => $this->note,
            ]);

            foreach ($this->produkTerpilih as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $produk = Product::find($item['id']);
                $produk->decrement('stock', $item['quantity']);
            }

            DB::commit();
            session()->flash('success', 'Transaksi berhasil disimpan!');
            return redirect(TransactionResource::getUrl('index'));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function tampilproduk()
    {
        $this->tampil = true;
    }

    public function render()
    {
        $produks = [];

        if (strlen($this->search) > 1) {
            $produks = Product::where('name', 'like', '%' . $this->search . '%')->get();
        } elseif ($this->tampil) {
            $produks = Product::limit(5)->get();
        }

        return view('livewire.create-transaksi', compact('produks'));
    }
}
