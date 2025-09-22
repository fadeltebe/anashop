<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BannerProduct extends Model
{
    protected $table = 'banner_product';

    protected $fillable = [
        'banner_id',
        'product_id',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
