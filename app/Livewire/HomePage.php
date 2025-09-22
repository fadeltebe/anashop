<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;

class HomePage extends Component
{
    use WithPagination;

    public $perPage = 12;
    protected $paginationTheme = 'tailwind';

    /**
     * Reset pagination jika jumlah data per halaman berubah
     */
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Load more produk rekomendasi (infinite scroll)
     */
    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function render()
    {
        return view('livewire.home-page', [
            // Kategori aktif
            'categories' => cache()->remember(
                'active_categories',
                600,
                fn() => Category::where('is_active', true)->get()
            ),

            // Produk flash sale (harga diskon lebih kecil dari harga normal)
            'flashSales' => Product::flashSale()
                ->latest()
                ->take(6)
                ->get(),

            // Produk unggulan (terlaris berdasarkan sold_count)
            'featuredProducts' => Product::featured()
                ->take(6)
                ->get(),

            // Produk rekomendasi (infinite scroll)
            'recommendedProducts' => Product::with('category', 'banners')
                ->latest()
                ->paginate($this->perPage),

            // Banner aktif
            'banners' => cache()->remember(
                'active_banners',
                600,
                fn() => Banner::with('products')->where('is_active', true)->get()
            ),
        ]);
    }
}
