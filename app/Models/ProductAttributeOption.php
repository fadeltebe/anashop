<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeOption extends Model
{
    protected $fillable = ['product_id', 'attribute_id', 'values'];

    protected $casts = [
        'values' => 'array',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
