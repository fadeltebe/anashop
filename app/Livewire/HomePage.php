<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\ProductCollection;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class HomePage extends Component
{
    public int $loadedCount = 12;

    public Collection $recommendedProducts;

    public bool $hasMorePages = true;

    protected const LOAD_INCREMENT = 12;

    // 👉 Tambahkan properti collections
    public $collections;

    public function mount(): void
    {
        // ----- RECOMMENDED PRODUCT SCROLL -----
        $this->recommendedProducts = $this->loadProducts(0, $this->loadedCount);

        if (Product::count() <= $this->loadedCount) {
            $this->hasMorePages = false;
        }

        // ----- PRODUCT COLLECTIONS -----
        $this->collections = ProductCollection::with('items.product')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    protected function loadProducts(int $skip, int $take): Collection
    {
        return Product::latest()
            ->skip($skip)
            ->take($take)
            ->get();
    }

    public function loadMore(): void
    {
        if (!$this->hasMorePages) {
            return;
        }

        $newProducts = $this->loadProducts($this->loadedCount, self::LOAD_INCREMENT);

        if ($newProducts->isEmpty()) {
            $this->hasMorePages = false;
            return;
        }

        $this->recommendedProducts = $this->recommendedProducts->merge($newProducts);

        $this->loadedCount += self::LOAD_INCREMENT;
    }

    public function render()
    {
        return view('livewire.home-page', [
            'banners' => Banner::with('products')->where('is_active', true)->get(),
            'categories' => Category::where('is_active', true)->get(),

            // 🔥 Anda bisa hapus liveProducts/flashSales/featuredProducts
            // karena seluruh logikanya pindah ke ProductCollection
            // tetapi untuk sementara saya biarkan agar tidak error
            'liveProducts' => Product::live()->latest()->take(6)->get(),
            'flashSales' => Product::flashSale()->latest()->take(6)->get(),
            'featuredProducts' => Product::featured()->take(6)->get(),

            // Jika view Anda masih butuh collections, kirimkan
            'collections' => $this->collections,
        ]);
    }
}
