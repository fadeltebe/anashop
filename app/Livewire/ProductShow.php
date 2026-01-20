<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
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
    public Collection $availablePhotos;

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
        $this->availableVariants = collect();

        // Load data
        $this->loadPhotos();
        $this->loadAvailableVariants();

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
     * Ambil variant pertama sebagai default
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
     * Load semua variant untuk ditampilkan sebagai pilihan
     * ====================================================== */
    protected function loadAvailableVariants(): void
    {
        if ($this->product->has_variant) {
            // ✅ Hanya ambil variant yang tidak ter-soft delete
            $this->availableVariants = $this->product->variants()
                ->whereNull('deleted_at')
                ->get();
        } else {
            $this->availableVariants = collect();
        }
    }

    /* ======================================================
     * LOAD PHOTOS
     * Load semua foto produk (thumbnail + photos tambahan)
     * ====================================================== */
    protected function loadPhotos(): void
    {
        $photos = [];

        // Thumbnail utama
        if ($this->product->thumbnail) {
            $photos[] = $this->product->thumbnail;
        }

        // Foto tambahan dari field 'photos' (JSON array)
        if ($this->product->photos) {
            $extra = is_string($this->product->photos)
                ? json_decode($this->product->photos, true)
                : $this->product->photos;

            if (is_array($extra)) {
                $photos = array_merge($photos, $extra);
            }
        }

        $this->availablePhotos = collect($photos)->filter()->unique()->values();
    }

    /* ======================================================
     * SELECT PHOTO
     * Ganti foto utama saat user klik thumbnail
     * ====================================================== */
    public function selectPhoto(int $index): void
    {
        if ($this->availablePhotos->has($index)) {
            $this->selectedPhotoIndex = $index;
            $this->mainImage = asset('storage/' . $this->availablePhotos[$index]);
        }
    }

    /* ======================================================
     * SELECT VARIANT
     * User klik variant button
     * ====================================================== */
    public function selectVariant(int $variantId): void
    {
        $variant = $this->product->variants()
            ->where('id', $variantId)
            ->whereNull('deleted_at')
            ->first();

        if ($variant) {
            $this->activeVariant = $variant;
            $this->selectedVariantName = $variant->variant_name;
            $this->stock = $variant->stock;

            // Reset quantity jika melebihi stock baru
            $this->quantity = min($this->quantity, $this->stock);

            // Update main image jika variant punya foto sendiri
            if ($variant->image) {
                $this->mainImage = asset('storage/' . $variant->image);
                $this->selectedPhotoIndex = 0;
            } else {
                // Kembali ke foto produk utama
                $this->mainImage = $this->getMainImageUrl();
            }
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
            // ✅ Panggil CartService dengan variant_id (bukan string)
            app(\App\Services\CartService::class)->addItem(
                $this->product->id,
                $this->activeVariant->id, // ✅ Kirim variant_id
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
     * Langsung ke checkout
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
     * Helper untuk mendapatkan URL foto utama
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
            return asset('storage/' . $this->availablePhotos->first());
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
