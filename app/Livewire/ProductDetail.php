<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductDetail extends Component
{
    public Product $product;
    public $selectedVariant = null;
    public $selectedSize = null;
    public $stock = null;
    public $quantity = 1;
    public $mainImage;
    public $selectedPhotoIndex = 0;

    public Collection $uniqueVariants;
    public Collection $uniqueSizes;
    public Collection $availablePhotos;

    public function mount($slug)
    {
        try {
            // Ambil product dengan relasi yang diperlukan
            $this->product = Product::with(['items', 'category'])
                ->where('slug', $slug)
                ->firstOrFail();

            // Inisialisasi collections
            $this->uniqueVariants = collect([]);
            $this->uniqueSizes = collect([]);
            $this->availablePhotos = collect([]);

            // Load data unik dan foto
            $this->loadUniqueData();
            $this->loadPhotos();

            // Set main image
            $this->mainImage = $this->getMainImageProperty();
            $this->selectedPhotoIndex = 0;

            // Set stock awal
            $this->updateStock();
        } catch (\Exception $e) {
            abort(404, 'Product not found');
        }
    }

    protected function loadUniqueData()
    {
        // Load unique variants
        $this->uniqueVariants = $this->product->items
            ->pluck('variant')
            ->filter()
            ->unique()
            ->values();

        // Load unique sizes berdasarkan variant yang dipilih
        if ($this->selectedVariant) {
            $this->uniqueSizes = $this->product->items
                ->where('variant', $this->selectedVariant)
                ->pluck('size')
                ->filter()
                ->unique()
                ->values();
        } else {
            // Jika tidak ada variant dipilih, ambil semua size unik
            $this->uniqueSizes = $this->product->items
                ->pluck('size')
                ->filter()
                ->unique()
                ->values();
        }
    }

    protected function loadPhotos()
    {
        $photos = [];

        // Ambil thumbnail sebagai foto utama
        if ($this->product->thumbnail) {
            $photos[] = $this->product->thumbnail;
        }

        // Ambil foto tambahan dari field photos
        if ($this->product->photos) {
            $additionalPhotos = is_string($this->product->photos)
                ? json_decode($this->product->photos, true)
                : $this->product->photos;

            if (is_array($additionalPhotos)) {
                $photos = array_merge($photos, $additionalPhotos);
            }
        }

        $this->availablePhotos = collect($photos)->filter()->unique()->values();
    }

    public function selectPhoto($index)
    {
        $this->selectedPhotoIndex = $index;
        if ($this->availablePhotos->has($index)) {
            $this->mainImage = asset('storage/' . $this->availablePhotos[$index]);
        }
    }

    public function selectVariant($variant)
    {
        $this->selectedVariant = $variant;
        $this->selectedSize = null; // Reset size ketika variant berubah
        $this->loadUniqueData();
        $this->updateStock();
    }

    public function selectSize($size)
    {
        $this->selectedSize = $size;
        $this->updateStock();
    }

    public function updateStock()
    {
        $items = $this->product->items;

        // Filter berdasarkan variant jika dipilih
        if ($this->selectedVariant) {
            $items = $items->where('variant', $this->selectedVariant);
        }

        // Filter berdasarkan size jika dipilih
        if ($this->selectedSize) {
            $items = $items->where('size', $this->selectedSize);
        }

        // Ambil item pertama yang sesuai kriteria
        $item = $items->first();

        // Set stock
        $this->stock = $item ? $item->stock : ($this->product->stock ?? 0);

        // Reset quantity jika melebihi stock
        if ($this->quantity > $this->stock) {
            $this->quantity = max(1, $this->stock);
        }
    }

    public function increaseQuantity()
    {
        if ($this->quantity < $this->stock) {
            $this->quantity++;
        }
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    // public function addToCart()
    // {
    //     // Validasi stock
    //     if ($this->stock <= 0) {
    //         session()->flash('error', 'Produk sedang tidak tersedia');
    //         return;
    //     }

    //     if ($this->quantity > $this->stock) {
    //         session()->flash('error', 'Quantity melebihi stock yang tersedia');
    //         return;
    //     }

    //     // Logic untuk menambah ke keranjang
    //     // Implementasi sesuai kebutuhan aplikasi Anda

    //     session()->flash('success', 'Produk berhasil ditambahkan ke keranjang');
    // }

    public function addToCart()
    {
        if ($this->stock <= 0) {
            session()->flash('error', 'Produk sedang tidak tersedia');
            return;
        }

        if ($this->quantity > $this->stock) {
            session()->flash('error', 'Quantity melebihi stock yang tersedia');
            return;
        }

        try {
            $cartService = app(\App\Services\CartService::class);
            $cartService->addItem(
                $this->product->id,
                $this->quantity,
                $this->selectedVariant,
                $this->selectedSize
            );

            session()->flash('success', 'Produk berhasil ditambahkan ke keranjang');

            // Emit event untuk update cart badge
            $this->dispatch('cartUpdated');

            // Reset quantity
            $this->quantity = 1;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan produk ke keranjang');
        }
    }

    public function getMainImageProperty()
    {
        return $this->availablePhotos->first()
            ? asset('storage/' . $this->availablePhotos->first())
            : asset('images/default-product.png');
    }



    public function render()
    {
        return view('livewire.product-detail');
    }
}
