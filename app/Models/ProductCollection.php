<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\ProductCollectionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'start_at',
        'end_at',
        'sort_order',
        'max_items',
        'display_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'sort_order' => 'integer',
        'max_items' => 'integer',
    ];

    /**
     * Relasi many-to-many dengan Product (via pivot table)
     * PENTING: Pakai 'collection_id' sesuai migration!
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_collection_items', 'collection_id', 'product_id')
            ->withPivot('sort_order', 'is_active')
            ->withTimestamps()
            ->orderBy('product_collection_items.sort_order');
    }

    /**
     * Relasi hasMany ke ProductCollectionItem
     * PENTING: Foreign key 'collection_id' sesuai migration!
     */
    public function items(): HasMany
    {
        return $this->hasMany(ProductCollectionItem::class, 'collection_id')
            ->orderBy('sort_order');
    }

    /**
     * Alias untuk items()
     */
    public function productCollectionItems(): HasMany
    {
        return $this->items();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRunning($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                    ->orWhere(function ($subQ) use ($now) {
                        $subQ->where('start_at', '<=', $now)
                            ->where(function ($endQ) use ($now) {
                                $endQ->whereNull('end_at')
                                    ->orWhere('end_at', '>=', $now);
                            });
                    });
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function isRunning(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if (is_null($this->start_at) && is_null($this->end_at)) {
            return true;
        }

        if ($this->start_at && $this->start_at->isFuture()) {
            return false;
        }

        if ($this->end_at && $this->end_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if (is_null($this->start_at) && is_null($this->end_at)) {
            return 'permanent';
        }

        $now = Carbon::now();

        if ($this->start_at && $this->start_at->isFuture()) {
            return 'upcoming';
        }

        if ($this->end_at && $this->end_at->isPast()) {
            return 'ended';
        }

        return 'running';
    }

    public function getLimitedProducts()
    {
        return $this->products()->limit($this->max_items ?? 12)->get();
    }
}
