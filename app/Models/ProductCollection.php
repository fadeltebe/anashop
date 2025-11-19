<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

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

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_collection_items')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('product_collection_items.sort_order');
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
        return $this->products()->limit($this->max_items)->get();
    }
}
