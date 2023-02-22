<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'product_name',
        'product_price',
        'total_price',
        'status'
    ];
}
