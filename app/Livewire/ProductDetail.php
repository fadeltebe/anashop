<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductDetail extends Component
{
    public Product $product;

    // Selection state
    public ?string $selectedVariantName = null;

    // Active variant (SINGLE SOURCE OF TRUTH)
    public ?ProductVariant $activeVariant = null;

    // Stock & quantity
    public int $stock = 0;
    public int $quantity = 1;

    // Images
    public string $mainImage;
    public int $selectedPhotoIndex = 0;
    public Collection $availablePhotos;

    // UI data
    public Collection $availableVariants;

    /* ======================================================
     * MOUNT
     * ====================================================== */
    public function mount(string $slug): void
    {
        $this->product = Product::with(['variants', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->availablePhotos = collect();
        $this->availableVariants = collect();

        $this->loadPhotos();
        $this->loadAvailableVariants();

        // Set initial active variant
        $this->activeVariant = $this->getInitialVariant();

        // ✅ Validasi activeVariant tidak null
        if (!$this->activeVariant) {
            abort(404, 'Produk tidak memiliki varian yang valid.');
        }

        $this->stock = $this->activeVariant->stock ?? 0;
        $this->selectedVariantName = $this->activeVariant->variant_name;

        // Main image
        $this->mainImage = $this->getMainImageProperty();
    }

    /* ======================================================
     * INITIAL VARIANT
     * ====================================================== */
    protected function getInitialVariant(): ?ProductVariant
    {
        // SINGLE PRODUCT → Default variant
        if (!$this->product->has_variant) {
            return $this->product->variants()
                ->where('variant_name', 'Default')
                ->first();
        }

        // MULTI VARIANT → ambil harga termurah atau yang pertama
        return $this->product->variants()
            ->orderBy('sale_price', 'asc')
            ->first();
    }

    /* ======================================================
     * LOAD AVAILABLE VARIANTS
     * ====================================================== */
    protected function loadAvailableVariants(): void
    {
        // Hanya untuk produk dengan variasi
        if ($this->product->has_variant) {
            $this->availableVariants = $this->product->variants()
                ->where('stock', '>', 0) // Hanya tampilkan yang ada stoknya
                ->get();
        } else {
            $this->availableVariants = collect();
        }
    }

    /* ======================================================
     * LOAD PHOTOS
     * ====================================================== */
    protected function loadPhotos(): void
    {
        $photos = [];

        // Thumbnail utama
        if ($this->product->thumbnail) {
            $photos[] = $this->product->thumbnail;
        }

        // Foto tambahan dari product
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
     * IMAGE HANDLER
     * ====================================================== */
    public function selectPhoto(int $index): void
    {
        if ($this->availablePhotos->has($index)) {
            $this->selectedPhotoIndex = $index;
            $this->mainImage = asset('storage/' . $this->availablePhotos[$index]);
        }
    }

    /* ======================================================
     * VARIANT SELECTION
     * ====================================================== */
    public function selectVariant(int $variantId): void
    {
        $variant = $this->product->variants()->find($variantId);

        if ($variant) {
            $this->activeVariant = $variant;
            $this->selectedVariantName = $variant->variant_name;
            $this->stock = $variant->stock;
            $this->quantity = min($this->quantity, $this->stock);

            // Update main image jika varian punya foto sendiri
            if ($variant->image) {
                $this->mainImage = asset('storage/' . $variant->image);
                $this->selectedPhotoIndex = 0;
            }
        }
    }

    /* ======================================================
     * QUANTITY
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
     * CART
     * ====================================================== */
    public function addToCart(): void
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan login terlebih dahulu untuk menambahkan barang ke keranjang.');
            $this->redirect(route('login') . '?intended=' . urlencode(request()->url()));
            return;
        }

        if (!$this->activeVariant || $this->stock <= 0) {
            session()->flash('error', 'Produk tidak tersedia.');
            return;
        }

        if ($this->quantity > $this->stock) {
            session()->flash('error', 'Jumlah melebihi stok tersedia.');
            return;
        }

        try {
            app(\App\Services\CartService::class)->addItem(
                $this->product->id,
                $this->activeVariant->id,
                $this->quantity
            );

            session()->flash('success', 'Produk berhasil ditambahkan ke keranjang.');
            $this->dispatch('cartUpdated');
            $this->quantity = 1;
        } catch (\Throwable $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menambahkan ke keranjang.');
        }
    }

    public function buyNow()
    {
        $this->addToCart();

        if (session()->has('success')) {
            return redirect()->route('checkout');
        }
    }

    /* ======================================================
     * COMPUTED
     * ====================================================== */
    public function getMainImageProperty(): string
    {
        // Prioritas: foto varian aktif → thumbnail produk → foto pertama → default
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
        return view('livewire.product-detail');
    }
}
