<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class FlashSalePage extends Component
{
    use WithPagination;

    public $perPage = 12; // jumlah produk yang ditampilkan
    protected $paginationTheme = 'tailwind';

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function render()
    {
        // Produk dengan diskon (flash sale)
        $products = Product::whereColumn('discount_price', '<', 'price')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.flash-sale-page', [
            'products' => $products,
        ]);
    }
}
