<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ============================================================================
 * FLUTTER APP API: WISHLIST CONTROLLER (Screen I)
 * ============================================================================
 * Handles:
 * - Saved shoes grid
 * - Toggle (Add / Remove) via heart icon
 * - Move saved shoe to cart
 * ============================================================================
 */
class FlutterWishlistController extends Controller
{
    /**
     * GET /api/v1/flutter/wishlist
     * Returns all saved shoes for authenticated customer
     */
    public function getWishlist(Request $request)
    {
        $customerId = auth('sanctum')->id();

        $wishlists = Wishlist::where('web_customer_id', $customerId)
            ->with(['product.brand', 'product.category_relation', 'product.webImages'])
            ->latest('id')
            ->get();

        $items = $wishlists->map(function ($w) {
            $p = $w->product;
            if (!$p) return null;

            $price = (float) ($p->web_sale_price ?: ($p->sale_price_per_piece ?: 0));
            $firstImg = $p->webImages->first();

            return [
                'wishlist_id'     => $w->id,
                'product_id'      => $p->id,
                'name'            => $p->item_name,
                'brand_name'      => $p->brand->name ?? 'Young Fashion',
                'category_name'   => $p->category_relation->name ?? 'Footwear',
                'price'           => $price,
                'formatted_price' => 'Rs. ' . number_format($price, 0),
                'image_url'       => $firstImg ? asset('uploads/products/' . $firstImg->image_path) : asset('assets/images/default-shoe.png'),
                'rating'          => 4.8,
                'in_stock'        => true,
            ];
        })->filter()->values();

        return response()->json([
            'status' => true,
            'total'  => $items->count(),
            'items'  => $items
        ]);
    }

    /**
     * POST /api/v1/flutter/wishlist/toggle
     * Heart icon tap: adds if not present, removes if present
     */
    public function toggleWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $customerId = auth('sanctum')->id();
        $productId = $request->product_id;

        $existing = Wishlist::where('web_customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'status'        => true,
                'is_wishlisted' => false,
                'message'       => 'Removed from wishlist'
            ]);
        } else {
            Wishlist::create([
                'web_customer_id' => $customerId,
                'product_id'      => $productId,
            ]);

            return response()->json([
                'status'        => true,
                'is_wishlisted' => true,
                'message'       => 'Added to wishlist'
            ]);
        }
    }
}
