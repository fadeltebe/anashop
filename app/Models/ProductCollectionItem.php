<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCollectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',  // GANTI dari product_collection_id
        'product_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * PENTING: Foreign key 'collection_id' sesuai migration!
     */
    public function collection()
    {
        return $this->belongsTo(ProductCollection::class, 'collection_id');
    }

    /**
     * Alias untuk collection() - untuk kompatibilitas
     */
    public function productCollection()
    {
        return $this->collection();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
