<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCustomerCode()
    {
        $lastCustomer = self::orderBy('id', 'desc')->first();
        if (!$lastCustomer) {
            return 'CUST0001';
        }

        $lastCode = $lastCustomer->customer_code;
        $number = (int) substr($lastCode, 4);
        $newNumber = $number + 1;

        return 'CUST' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
