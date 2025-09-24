<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class RecommendedProductScroll extends Component
{
    public Collection $products;
    public int $loadedCount = 12;
    public bool $hasMorePages = true;

    protected const LOAD_INCREMENT = 12;

    public function mount(): void
    {
        // Muat 12 produk awal
        $this->products = $this->loadProducts($this->loadedCount, 0);

        // Cek apakah ada lebih banyak data yang mungkin dimuat
        if (Product::count() <= $this->loadedCount) {
            $this->hasMorePages = false;
        }
    }

    // Metode Helper untuk Query
    protected function loadProducts(int $take, int $skip): Collection
    {
        // Ambil produk dan hitung total data tanpa batasan
        $totalProducts = Product::count();

        // Tentukan apakah masih ada halaman yang tersisa
        $this->hasMorePages = ($skip + $take) < $totalProducts;

        return Product::latest()
            ->skip($skip)
            ->take($take)
            ->get();
    }

    // Metode yang dipanggil oleh x-intersect
    public function loadMore(): void
    {
        // Hentikan jika tidak ada lagi produk
        if (!$this->hasMorePages) {
            return;
        }

        // Ambil produk berikutnya saja, dengan skip sebesar $loadedCount saat ini
        $newProducts = $this->loadProducts(self::LOAD_INCREMENT, $this->loadedCount);

        // Gabungkan produk baru ke koleksi yang sudah ada (Kunci Infinite Scroll)
        $this->products = $this->products->merge($newProducts);

        // Perbarui total produk yang sudah dimuat
        $this->loadedCount += self::LOAD_INCREMENT;
    }

    public function render()
    {
        // Hanya merender view khusus untuk produk rekomendasi
        return view('livewire.recommended-product-scroll');
    }
}
