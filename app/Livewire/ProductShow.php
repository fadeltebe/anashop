<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductShow extends Component
{
    public Product $product;

    // Active variant (SINGLE SOURCE OF TRUTH)
    public ?ProductVariant $activeVariant = null;
    public ?string $selectedVariantName = null;

    // Stock & quantity
    public int $stock = 0;
    public int $quantity = 1;

    // Images
    public string $mainImage = '';
    public int $selectedPhotoIndex = 0;
    public Collection $availablePhotos; // All photos (product + variants)
    public Collection $productPhotos;   // Only product photos
    public Collection $variantPhotos;   // Only variant photos with variant_id

    // UI data
    public Collection $availableVariants;

    /* ======================================================
     * MOUNT
     * ====================================================== */
    public function mount(string $slug): void
    {
        // Load product dengan relasi
        $this->product = Product::with(['variants', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Initialize collections
        $this->availablePhotos = collect();
        $this->productPhotos = collect();
        $this->variantPhotos = collect();
        $this->availableVariants = collect();

        // Load data
        $this->loadAvailableVariants();
        $this->loadPhotos();

        // Set initial active variant
        $this->activeVariant = $this->getInitialVariant();

        // Validasi variant harus ada
        if (!$this->activeVariant) {
            abort(404, 'Produk tidak memiliki varian yang valid.');
        }

        // Set initial state
        $this->stock = $this->activeVariant->stock ?? 0;
        $this->selectedVariantName = $this->activeVariant->variant_name;
        $this->mainImage = $this->getMainImageUrl();
    }

    /* ======================================================
     * GET INITIAL VARIANT
     * ====================================================== */
    protected function getInitialVariant(): ?ProductVariant
    {
        // SINGLE PRODUCT → Cari variant "Default"
        if (!$this->product->has_variant) {
            return $this->product->variants()
                ->where('variant_name', 'Default')
                ->whereNull('deleted_at')
                ->first();
        }

        // MULTI VARIANT → Ambil yang harga termurah atau pertama
        return $this->product->variants()
            ->whereNull('deleted_at')
            ->orderBy('sale_price', 'asc')
            ->first();
    }

    /* ======================================================
     * LOAD AVAILABLE VARIANTS
     * ====================================================== */
    protected function loadAvailableVariants(): void
    {
        if ($this->product->has_variant) {
            $this->availableVariants = $this->product->variants()
                ->whereNull('deleted_at')
                ->get();
        } else {
            $this->availableVariants = collect();
        }
    }

    /* ======================================================
     * LOAD PHOTOS
     * Load foto produk + foto variant
     * ====================================================== */
    protected function loadPhotos(): void
    {
        $allPhotos = [];

        // ✅ 1. Product Photos (thumbnail + additional photos)
        $productPhotosList = [];

        if ($this->product->thumbnail) {
            $productPhotosList[] = [
                'url' => $this->product->thumbnail,
                'type' => 'product',
                'variant_id' => null,
            ];
        }

        if ($this->product->photos) {
            $extra = is_string($this->product->photos)
                ? json_decode($this->product->photos, true)
                : $this->product->photos;

            if (is_array($extra)) {
                foreach ($extra as $photo) {
                    $productPhotosList[] = [
                        'url' => $photo,
                        'type' => 'product',
                        'variant_id' => null,
                    ];
                }
            }
        }

        // ✅ 2. Variant Photos (dari setiap variant yang punya image)
        $variantPhotosList = [];

        foreach ($this->availableVariants as $variant) {
            if ($variant->image) {
                $variantPhotosList[] = [
                    'url' => $variant->image,
                    'type' => 'variant',
                    'variant_id' => $variant->id,
                    'variant_name' => $variant->variant_name,
                ];
            }
        }

        // ✅ 3. Combine: Variant photos first, then product photos
        $allPhotos = array_merge($variantPhotosList, $productPhotosList);

        // Store in collections
        $this->availablePhotos = collect($allPhotos);
        $this->productPhotos = collect($productPhotosList);
        $this->variantPhotos = collect($variantPhotosList);
    }

    /* ======================================================
     * SELECT PHOTO
     * Ganti foto utama + auto-select variant jika foto variant
     * ====================================================== */
    public function selectPhoto(int $index): void
    {
        if (!$this->availablePhotos->has($index)) {
            return;
        }

        $photo = $this->availablePhotos[$index];

        // Update selected index
        $this->selectedPhotoIndex = $index;

        // Update main image
        $this->mainImage = asset('storage/' . $photo['url']);

        // ✅ Jika foto ini adalah foto variant, auto-select variant tersebut
        if ($photo['type'] === 'variant' && $photo['variant_id']) {
            $this->selectVariant($photo['variant_id']);
        }
    }

    /* ======================================================
     * SELECT VARIANT
     * User klik variant button atau klik foto variant
     * ====================================================== */
    public function selectVariant(int $variantId): void
    {
        $variant = $this->product->variants()
            ->where('id', $variantId)
            ->whereNull('deleted_at')
            ->first();

        if (!$variant) {
            return;
        }

        // Update active variant
        $this->activeVariant = $variant;
        $this->selectedVariantName = $variant->variant_name;
        $this->stock = $variant->stock;

        // Reset quantity jika melebihi stock baru
        $this->quantity = min($this->quantity, $this->stock);

        // ✅ Update main image & selected photo index
        if ($variant->image) {
            // Jika variant punya foto sendiri
            $this->mainImage = asset('storage/' . $variant->image);

            // Find index of this variant photo
            $photoIndex = $this->availablePhotos->search(function ($photo) use ($variant) {
                return $photo['type'] === 'variant' && $photo['variant_id'] === $variant->id;
            });

            if ($photoIndex !== false) {
                $this->selectedPhotoIndex = $photoIndex;
            }
        } else {
            // Kembali ke foto produk utama
            $this->mainImage = $this->getMainImageUrl();
            $this->selectedPhotoIndex = 0;
        }
    }

    /* ======================================================
     * QUANTITY CONTROLS
     * ====================================================== */
    public function increaseQuantity(): void
    {
        if ($this->quantity < $this->stock) {
            $this->quantity++;
        }
    }

    public function decreaseQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /* ======================================================
     * ADD TO CART
     * ====================================================== */
    public function addToCart(): void
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan login terlebih dahulu untuk menambahkan barang ke keranjang.');
            $this->redirect(route('login') . '?intended=' . urlencode(request()->url()));
            return;
        }

        // Validasi
        if (!$this->activeVariant || $this->stock <= 0) {
            session()->flash('error', 'Produk tidak tersedia.');
            return;
        }

        if ($this->quantity > $this->stock) {
            session()->flash('error', 'Jumlah melebihi stok tersedia.');
            return;
        }

        try {
            // Panggil CartService dengan variant_id
            app(\App\Services\CartService::class)->addItem(
                $this->product->id,
                $this->activeVariant->id,
                $this->quantity
            );

            session()->flash('success', 'Produk berhasil ditambahkan ke keranjang.');

            // Dispatch event untuk update cart counter di navbar
            $this->dispatch('cartUpdated');

            // Reset quantity
            $this->quantity = 1;
        } catch (\Throwable $e) {
            Log::error('Add to cart error: ' . $e->getMessage(), [
                'product_id' => $this->product->id,
                'variant_id' => $this->activeVariant->id,
                'quantity' => $this->quantity,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Gagal menambahkan ke keranjang: ' . $e->getMessage());
        }
    }

    /* ======================================================
     * BUY NOW
     * ====================================================== */
    public function buyNow()
    {
        $this->addToCart();

        if (session()->has('success')) {
            return redirect()->route('checkout');
        }
    }

    /* ======================================================
     * GET MAIN IMAGE URL
     * ====================================================== */
    protected function getMainImageUrl(): string
    {
        // Priority:
        // 1. Foto variant aktif (jika ada)
        // 2. Thumbnail produk
        // 3. Foto pertama di gallery
        // 4. Default placeholder

        if ($this->activeVariant && $this->activeVariant->image) {
            return asset('storage/' . $this->activeVariant->image);
        }

        if ($this->availablePhotos->first()) {
            return asset('storage/' . $this->availablePhotos->first()['url']);
        }

        return asset('images/default-product.png');
    }

    /* ======================================================
     * RENDER
     * ====================================================== */
    public function render()
    {
        return view('livewire.product-show');
    }
}
