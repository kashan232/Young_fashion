<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_row_id',
        'variant_name',
        'variant_size',
        'variant_color',
        'variant_barcode',
        'actual_price',
        'discount_percentage',
        'discount_amount',
        'total_discount',
        'final_price',
        'date',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
