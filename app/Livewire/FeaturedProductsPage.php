<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class FeaturedProductsPage extends Component
{
    use WithPagination;

    public $perPage = 12;
    protected $paginationTheme = 'tailwind';

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function render()
    {
        // Produk unggulan/terlaris → contoh: berdasarkan penjualan terbanyak
        $products = Product::orderBy('id', 'desc') // pastikan ada kolom sold_count
            ->paginate($this->perPage);

        return view('livewire.featured-products-page', [
            'products' => $products,
        ]);
    }
}
