<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class HomePage extends Component
{
    // Mengganti $perPage menjadi $loadedCount
    public int $loadedCount = 12;

    // Properti untuk menyimpan daftar produk yang akan di-scroll
    // WAJIB menggunakan Collection agar bisa di-merge
    public Collection $recommendedProducts;

    protected const LOAD_INCREMENT = 12;

    public bool $hasMorePages = true;

    // Hapus protected $listeners = ['load-more' => 'loadMore']; karena kita pakai x-intersect

    public function mount(): void
    {
        // Muat 12 produk awal saat komponen pertama kali dimuat
        $this->recommendedProducts = $this->loadProducts(0, $this->loadedCount);

        // Cek apakah total produk kurang dari jumlah awal
        if (Product::count() <= $this->loadedCount) {
            $this->hasMorePages = false;
        }
    }

    // Metode Helper untuk Query
    protected function loadProducts(int $skip, int $take): Collection
    {
        return Product::latest()
            ->skip($skip)
            ->take($take)
            ->get();
    }

    // Metode yang dipanggil oleh x-intersect
    public function loadMore(): void
    {
        // Pastikan tidak memuat jika sudah habis
        if (!$this->hasMorePages) {
            return;
        }

        // Ambil produk berikutnya saja
        $newProducts = $this->loadProducts($this->loadedCount, self::LOAD_INCREMENT);

        // Jika tidak ada lagi produk yang tersisa
        if ($newProducts->isEmpty()) {
            $this->hasMorePages = false;
            return;
        }

        // Gabungkan produk baru ke koleksi yang sudah ada (INI KUNCI INFINITE SCROLL)
        $this->recommendedProducts = $this->recommendedProducts->merge($newProducts);

        // Perbarui total produk yang sudah dimuat
        $this->loadedCount += self::LOAD_INCREMENT;
    }

    public function render()
    {
        // recommendedProducts sudah ada sebagai properti publik
        return view('livewire.home-page', [
            'categories' => Category::where('is_active', true)->get(),
            'flashSales' => Product::flashSale()->latest()->take(6)->get(),
            'featuredProducts' => Product::featured()->take(6)->get(),
            'banners' => Banner::with('products')->where('is_active', true)->get(),
            // TIDAK PERLU mengambil recommendedProducts lagi di render() karena sudah ada di properti $this->recommendedProducts
        ]);
    }
}
