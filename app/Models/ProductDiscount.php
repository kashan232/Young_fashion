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

    /**
     * Resolve effective retail price & discount details for a base price
     * from a collection of active ProductDiscount models (status = 1).
     */
    public static function resolveForPrice($activeDiscounts, float $basePrice, ?string $variantRowId = null, ?string $variantBarcode = null): array
    {
        if (! $activeDiscounts || $activeDiscounts->isEmpty() || $basePrice <= 0) {
            return [
                'has_discount' => false,
                'original_price' => $basePrice,
                'final_price' => $basePrice,
                'discount_amount' => 0.0,
                'discount_percentage' => 0.0,
            ];
        }

        // If variant context is provided: prefer exact variant match, fallback to product-level discount only.
        // If no variant context is provided: prefer product-level discount, fallback to first active discount.
        $matched = null;
        if ($variantRowId || $variantBarcode) {
            $matched = $activeDiscounts->first(function ($d) use ($variantRowId, $variantBarcode) {
                return ($variantRowId && (string) $d->variant_row_id === (string) $variantRowId)
                    || ($variantBarcode && (string) $d->variant_barcode === (string) $variantBarcode);
            });
            if (! $matched) {
                $matched = $activeDiscounts->first(function ($d) {
                    return empty($d->variant_row_id);
                });
            }
        } else {
            $matched = $activeDiscounts->first(function ($d) {
                return empty($d->variant_row_id);
            }) ?: $activeDiscounts->first();
        }

        if (! $matched) {
            return [
                'has_discount' => false,
                'original_price' => $basePrice,
                'final_price' => $basePrice,
                'discount_amount' => 0.0,
                'discount_percentage' => 0.0,
            ];
        }

        $discPct = (float) ($matched->discount_percentage ?? 0);
        $discFixed = (float) ($matched->discount_amount ?? 0);
        $storedActual = (float) ($matched->actual_price ?? 0);

        if ($storedActual > 0 && abs($storedActual - $basePrice) < 0.01) {
            $finalPrice = (float) ($matched->final_price ?? $basePrice);
            $discAmt = (float) ($matched->total_discount ?? max(0, $basePrice - $finalPrice));
        } else {
            $pctAmt = $discPct > 0 ? round($basePrice * ($discPct / 100), 2) : 0.0;
            $discAmt = round(min($basePrice, $pctAmt + $discFixed), 2);
            $finalPrice = round(max(0, $basePrice - $discAmt), 2);
            if ($discPct <= 0 && $basePrice > 0 && $discAmt > 0) {
                $discPct = round(($discAmt / $basePrice) * 100, 2);
            }
        }

        return [
            'has_discount' => $discAmt > 0,
            'original_price' => $basePrice,
            'final_price' => $finalPrice,
            'discount_amount' => $discAmt,
            'discount_percentage' => $discPct,
        ];
    }
}
