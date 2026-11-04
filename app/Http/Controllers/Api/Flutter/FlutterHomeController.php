<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Wishlist;
use Illuminate\Http\Request;

/**
 * ============================================================================
 * FLUTTER APP API: HOME CONTROLLER (Screens B & C)
 * ============================================================================
 * Handles:
 * - Unified Home Payload in 1 super-fast API call
 * - Top Bar: user greeting, store location, unread notification count
 * - Banner Carousel (Sales & New Arrivals with click actions)
 * - Brand Chips (Nike, Adidas, Puma, Servis, Hush Puppies)
 * - Category Row (Sneakers, Running, Formal, Loafers, Sandals, Kids)
 * - "New Arrivals" horizontal list
 * - "Best Sellers" horizontal list
 * - Product Grid with image, heart icon, name, brand, price, rating
 * ============================================================================
 */
class FlutterHomeController extends Controller
{
    /**
     * GET /api/v1/flutter/home
     * Single unified endpoint to populate the entire Home Screen
     */
    public function index(Request $request)
    {
        $customerId = auth('sanctum')->id();
        $userWishlistIds = [];
        if ($customerId) {
            $userWishlistIds = Wishlist::where('web_customer_id', $customerId)
                ->pluck('product_id')
                ->toArray();
        }

        // 1. User Greeting & Notification
        $userName = auth('sanctum')->check() ? auth('sanctum')->user()->name : null;
        $greeting = $userName ? "Hello, {$userName} 👋" : "Welcome to Young Fashion 👋";

        // 2. Banner Carousel
        $banners = [
            [
                'id'         => 1,
                'title'      => 'Grand Season Sale',
                'subtitle'   => 'Up to 30% OFF on Casual Loafers & Sneakers',
                'image_url'  => asset('assets/images/banners/banner1.jpg'),
                'action_type'=> 'category', // 'category', 'product', 'url'
                'action_id'  => 1,
            ],
            [
                'id'         => 2,
                'title'      => 'Exclusive Hush Puppies',
                'subtitle'   => 'Premium Handcrafted Leather Penny Loafers',
                'image_url'  => asset('assets/images/banners/banner2.jpg'),
                'action_type'=> 'product',
                'action_id'  => 2,
            ]
        ];

        // 3. Brand Chips (Top Footwear Brands)
        $brands = Brand::select('id', 'name')
            ->take(8)
            ->get()
            ->map(function ($b) {
                return [
                    'id'        => $b->id,
                    'name'      => $b->name,
                    'logo_url'  => asset('assets/images/brands/' . strtolower(str_replace(' ', '-', $b->name)) . '.png'),
                ];
            });

        // 4. Categories Row (Sneakers, Running, Formal, Loafers, Sandals, Kids)
        $categories = Category::select('id', 'name')
            ->take(10)
            ->get()
            ->map(function ($c) {
                return [
                    'id'        => $c->id,
                    'name'      => $c->name,
                    'image_url' => asset('assets/images/categories/cat-' . $c->id . '.png'),
                ];
            });

        // 5. Query Active Shoes with Stocks & Images
        $productsQuery = Product::with(['category_relation', 'brand', 'webImages'])
            ->withSum('warehouseStocks as total_stock', 'total_pieces')
            ->whereNull('deleted_at');

        $allProducts = $productsQuery->latest('id')->take(20)->get();

        // Helper to format products for Flutter Cards
        $formatProductCard = function ($p) use ($userWishlistIds) {
            $price = (float) ($p->web_sale_price ?: ($p->sale_price_per_piece ?: $p->sale_price_per_box ?: 0));
            $mrp = (float) ($p->mrp_per_piece ?: ($price > 0 ? $price * 1.15 : 0));
            
            // Primary image
            $firstImg = $p->webImages->first();
            $imageUrl = $firstImg 
                ? asset('uploads/products/' . $firstImg->image_path)
                : asset('assets/images/default-shoe.png');

            $inStock = ((int) ($p->total_stock ?? 0)) > 0;

            return [
                'id'              => $p->id,
                'name'            => $p->item_name,
                'article_code'    => $p->item_code,
                'brand_name'      => $p->brand->name ?? 'Young Fashion',
                'category_name'   => $p->category_relation->name ?? 'Footwear',
                'price'           => $price,
                'original_price'  => $mrp > $price ? $mrp : null,
                'discount_percent'=> ($mrp > $price && $mrp > 0) ? round((($mrp - $price) / $mrp) * 100) : 0,
                'formatted_price' => 'Rs. ' . number_format($price, 0),
                'image_url'       => $imageUrl,
                'rating'          => 4.8,
                'reviews_count'   => 24,
                'in_stock'        => $inStock,
                'is_wishlisted'   => in_array($p->id, $userWishlistIds),
            ];
        };

        $formattedList = $allProducts->map($formatProductCard);

        // Split into New Arrivals, Best Sellers, and Product Grid
        $newArrivals = $formattedList->take(6)->values();
        $bestSellers = $formattedList->reverse()->take(6)->values();
        $productGrid = $formattedList->values();

        return response()->json([
            'status' => true,
            'data'   => [
                'top_bar' => [
                    'greeting'           => $greeting,
                    'location'           => 'Hyderabad, Pakistan',
                    'unread_notifications'=> 2,
                ],
                'banners'        => $banners,
                'brand_chips'    => $brands,
                'category_row'   => $categories,
                'new_arrivals'   => $newArrivals,
                'best_sellers'   => $bestSellers,
                'product_grid'   => $productGrid,
            ]
        ]);
    }
}
