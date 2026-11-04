<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ============================================================================
 * FLUTTER APP API: CART & PROMO CODE CONTROLLER (Screen F)
 * ============================================================================
 * Handles:
 * - Cart summary & stock validation
 * - Stepper (+ / - quantity) real-time calculation
 * - Promo code / coupon validation and discount application
 * - Subtotal, delivery charges, discount, grand total calculation
 * ============================================================================
 */
class FlutterCartController extends Controller
{
    /**
     * POST /api/v1/flutter/cart/calculate
     * Takes cart items array from Flutter local state, validates live stock,
     * applies coupon (if any), and returns the complete calculation breakdown.
     */
    public function calculateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.size'          => 'nullable|string',
            'items.*.color'         => 'nullable|string',
            'coupon_code'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validatedItems = [];
        $subtotal = 0;
        $totalPairs = 0;
        $hasOutOfStock = false;

        foreach ($request->items as $item) {
            $product = Product::with(['brand', 'webImages'])
                ->withSum('warehouseStocks as total_stock', 'total_pieces')
                ->find($item['product_id']);

            if (!$product) continue;

            $unitPrice = (float) ($product->web_sale_price ?: ($product->sale_price_per_piece ?: 0));
            $qty = (int) $item['quantity'];
            $lineTotal = $unitPrice * $qty;
            $stock = (int) ($product->total_stock ?? 0);

            $isAvailable = $stock >= $qty;
            if (!$isAvailable) {
                $hasOutOfStock = true;
            }

            $firstImg = $product->webImages->first();
            $imageUrl = $firstImg 
                ? asset('uploads/products/' . $firstImg->image_path)
                : asset('assets/images/default-shoe.png');

            $validatedItems[] = [
                'product_id'      => $product->id,
                'name'            => $product->item_name,
                'brand_name'      => $product->brand->name ?? 'Young Fashion',
                'size'            => $item['size'] ?? 'Standard',
                'color'           => $item['color'] ?? 'Default',
                'quantity'        => $qty,
                'unit_price'      => $unitPrice,
                'line_total'      => $lineTotal,
                'formatted_price' => 'Rs. ' . number_format($lineTotal, 0),
                'image_url'       => $imageUrl,
                'in_stock'        => $isAvailable,
                'available_stock' => $stock,
            ];

            $subtotal += $lineTotal;
            $totalPairs += $qty;
        }

        // Standard Delivery Rule: Free delivery above Rs. 4,000, else Rs. 200
        $deliveryFee = ($subtotal >= 4000 || $subtotal == 0) ? 0.00 : 200.00;

        // Coupon Discount Calculation
        $discountAmount = 0.00;
        $couponMessage = null;
        $couponApplied = false;

        if ($request->filled('coupon_code')) {
            $code = trim($request->coupon_code);
            $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

            if ($coupon) {
                if ($coupon->min_spend !== null && $subtotal < $coupon->min_spend) {
                    $couponMessage = "Minimum spend of Rs. {$coupon->min_spend} required for code '{$code}'.";
                } else {
                    if ($coupon->type === 'fixed') {
                        $discountAmount = (float) $coupon->value;
                    } elseif ($coupon->type === 'percent') {
                        $discountAmount = ($subtotal * (float)$coupon->value) / 100;
                    }
                    $discountAmount = min($discountAmount, $subtotal); // cannot exceed subtotal
                    $couponApplied = true;
                    $couponMessage = "Coupon '{$code}' applied successfully!";
                }
            } else {
                $couponMessage = "Invalid or expired promo code.";
            }
        }

        $grandTotal = max(0, ($subtotal - $discountAmount) + $deliveryFee);

        return response()->json([
            'status'         => true,
            'items'          => $validatedItems,
            'total_pairs'    => $totalPairs,
            'has_out_of_stock'=> $hasOutOfStock,
            'summary'        => [
                'subtotal'           => $subtotal,
                'delivery_fee'       => $deliveryFee,
                'discount'           => $discountAmount,
                'grand_total'        => $grandTotal,
                'formatted_subtotal' => 'Rs. ' . number_format($subtotal, 0),
                'formatted_delivery' => $deliveryFee == 0 ? 'FREE' : ('Rs. ' . number_format($deliveryFee, 0)),
                'formatted_discount' => 'Rs. ' . number_format($discountAmount, 0),
                'formatted_total'    => 'Rs. ' . number_format($grandTotal, 0),
                'coupon_applied'     => $couponApplied,
                'coupon_message'     => $couponMessage,
            ]
        ]);
    }
}
