<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VariantAttributeValue extends Pivot
{
    protected $table = 'variant_attribute_values';

    protected $fillable = [
        'product_variant_id',
        'attribute_value_id',
    ];
}
