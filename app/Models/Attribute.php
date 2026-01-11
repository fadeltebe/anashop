<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relasi ke nilai-nilai atribut (One-to-Many)
     * Contoh: Warna punya Merah, Kuning, Hijau.
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
