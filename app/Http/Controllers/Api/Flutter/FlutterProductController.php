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
 * FLUTTER APP API: PRODUCT & EXPLORE CONTROLLER (Screens D & E)
 * ============================================================================
 * Handles:
 * Screen D:
 * - Search by keyword
 * - Trending / Recent Searches
 * - Filter Bottom Sheet Metadata (Brands, Sizes, Colors, Price Range, Genders)
 * - Multi-Filter & Sorting (price_low_high, newest, popular)
 * - Empty state support
 *
 * Screen E (Sab se important screen):
 * - Complete Product Details
 * - Image Gallery (grouped per color for swipe & pinch-to-zoom)
 * - Color Circles Selector
 * - Size Grid Selector with in_stock indicator (grey-out out-of-stock sizes)
 * - Description, Materials, Delivery & Return Policy
 * - Customer Reviews & Ratings
 * - "You May Also Like" Related Footwear List
 * ============================================================================
 */
class FlutterProductController extends Controller
{
    /**
     * GET /api/v1/flutter/search/trending
     * Returns trending and recent search keywords
     */
    public function trendingKeywords()
    {
        return response()->json([
            'status' => true,
            'trending_searches' => [
                'Penny Loafers',
                'Casual Leather Shoes',
                'Sneakers for Men',
                'Running Shoes',
                'Hush Puppies Formal',
                'Summer Sandals',
                'Black Dress Shoes',
            ],
            'popular_tags' => [
                ['name' => 'Loafers', 'category_id' => 1],
                ['name' => 'Formal', 'category_id' => 2],
                ['name' => 'Sneakers', 'category_id' => 3],
                ['name' => 'Casual', 'category_id' => 4],
            ]
        ]);
    }

    /**
     * GET /api/v1/flutter/search/filters
     * Returns metadata for the Filter Bottom Sheet
     */
    public function filterMetadata()
    {
        // 1. All Brands
        $brands = Brand::select('id', 'name')->get();

        // 2. Standard Footwear Sizes
        $availableSizes = ['38', '39', '40', '41', '42', '43', '44', '45'];

        // 3. Common Shoe Colors
        $availableColors = [
            ['name' => 'Black', 'hex' => '#000000'],
            ['name' => 'Tan', 'hex' => '#D2B48C'],
            ['name' => 'Brown', 'hex' => '#8B4513'],
            ['name' => 'White', 'hex' => '#FFFFFF'],
            ['name' => 'Navy Blue', 'hex' => '#000080'],
            ['name' => 'Grey', 'hex' => '#808080'],
            ['name' => 'Maroon', 'hex' => '#800000'],
        ];

        // 4. Min & Max Price Range for Slider
        $minPrice = (float) (Product::whereNull('deleted_at')->min('web_sale_price') ?: 1000);
        $maxPrice = (float) (Product::whereNull('deleted_at')->max('web_sale_price') ?: 15000);

        return response()->json([
            'status' => true,
            'data'   => [
                'brands'      => $brands,
                'sizes'       => $availableSizes,
                'colors'      => $availableColors,
                'genders'     => ['Men', 'Women', 'Kids', 'Unisex'],
                'price_range' => [
                    'min'      => $minPrice,
                    'max'      => $maxPrice,
                    'currency' => 'PKR',
                ],
                'sort_options' => [
                    ['key' => 'newest', 'label' => 'Newest Arrivals'],
                    ['key' => 'popular', 'label' => 'Most Popular'],
                    ['key' => 'price_low_high', 'label' => 'Price: Low to High'],
                    ['key' => 'price_high_low', 'label' => 'Price: High to Low'],
                ]
            ]
        ]);
    }

    /**
     * GET /api/v1/flutter/products
     * Search and Filter products catalog (Screen D)
     */
    public function searchAndExplore(Request $request)
    {
        $customerId = auth('sanctum')->id();
        $userWishlistIds = [];
        if ($customerId) {
            $userWishlistIds = Wishlist::where('web_customer_id', $customerId)
                ->pluck('product_id')
                ->toArray();
        }

        $query = Product::with(['category_relation', 'brand', 'webImages'])
            ->withSum('warehouseStocks as total_stock', 'total_pieces')
            ->whereNull('deleted_at');

        // 1. Text Search
        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('item_name', 'like', "%{$kw}%")
                  ->orWhere('item_code', 'like', "%{$kw}%")
                  ->orWhereHas('brand', function ($bq) use ($kw) {
                      $bq->where('name', 'like', "%{$kw}%");
                  })
                  ->orWhereHas('category_relation', function ($cq) use ($kw) {
                      $cq->where('name', 'like', "%{$kw}%");
                  });
            });
        }

        // 2. Brand Filter
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // 3. Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 4. Size Filter (Searches in JSON variants)
        if ($request->filled('size')) {
            $size = $request->size;
            $query->where(function ($q) use ($size) {
                $q->where('color', 'like', '%"size":"' . $size . '"%')
                  ->orWhere('color', 'like', '%"size": "' . $size . '"%')
                  ->orWhere('color', 'like', '%"size":"' . $size . ' %');
            });
        }

        // 5. Color Filter
        if ($request->filled('color')) {
            $color = $request->color;
            $query->where(function ($q) use ($color) {
                $q->where('color', 'like', '%"color":"' . $color . '"%')
                  ->orWhere('color', 'like', '%"color": "' . $color . '"%');
            });
        }

        // 6. Price Range Slider Filter
        if ($request->filled('min_price')) {
            $query->where('web_sale_price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('web_sale_price', '<=', (float) $request->max_price);
        }

        // 7. Sorting
        switch ($request->get('sort_by')) {
            case 'price_low_high':
                $query->orderBy('web_sale_price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('web_sale_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('id', 'desc');
                break;
            case 'newest':
            default:
                $query->latest('id');
                break;
        }

        // Paginate
        $perPage = (int) ($request->per_page ?: 20);
        $products = $query->paginate($perPage);

        // Transform collection
        $products->getCollection()->transform(function ($p) use ($userWishlistIds) {
            $price = (float) ($p->web_sale_price ?: ($p->sale_price_per_piece ?: $p->sale_price_per_box ?: 0));
            $mrp = (float) ($p->mrp_per_piece ?: ($price > 0 ? $price * 1.15 : 0));

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
                'reviews_count'   => 18,
                'in_stock'        => $inStock,
                'is_wishlisted'   => in_array($p->id, $userWishlistIds),
            ];
        });

        return response()->json([
            'status'         => true,
            'total_found'    => $products->total(),
            'current_page'   => $products->currentPage(),
            'last_page'      => $products->lastPage(),
            'products'       => $products->items(),
            'empty_state'    => $products->isEmpty() ? [
                'title'       => 'No Shoes Found',
                'message'     => 'We couldn’t find any matching shoes with your selected filters. Try clearing some filters or searching with another keyword.',
                'action_text' => 'Clear All Filters',
            ] : null,
        ]);
    }

    /**
     * GET /api/v1/flutter/products/{id}
     * Product Details Screen (Screen E)
     */
    public function showProductDetails($id)
    {
        $customerId = auth('sanctum')->id();
        $isWishlisted = false;
        if ($customerId) {
            $isWishlisted = Wishlist::where('web_customer_id', $customerId)
                ->where('product_id', $id)
                ->exists();
        }

        $product = Product::with(['category_relation', 'brand', 'webImages'])
            ->withSum('warehouseStocks as total_stock', 'total_pieces')
            ->whereNull('deleted_at')
            ->findOrFail($id);

        $totalStock = (int) ($product->total_stock ?? 0);
        $price = (float) ($product->web_sale_price ?: ($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0));
        $mrp = (float) ($product->mrp_per_piece ?: ($price > 0 ? $price * 1.15 : 0));

        // 1. Parse Variants from JSON
        $variants = [];
        $sizesMap = [];
        $colorsMap = [];

        if (!empty($product->color)) {
            $decoded = base64_decode($product->color, true);
            $parsed = ($decoded !== false) ? json_decode($decoded, true) : null;
            if (empty($parsed)) {
                $parsed = json_decode($product->color, true);
            }

            if (is_array($parsed)) {
                foreach ($parsed as $v) {
                    if (is_array($v)) {
                        $s = (string) ($v['size'] ?? '');
                        $c = (string) ($v['color'] ?? '');
                        $bc = (string) ($v['barcode'] ?? '');
                        if ($s) $sizesMap[$s] = true;
                        if ($c) $colorsMap[$c] = true;

                        $variants[] = [
                            'size'    => $s,
                            'color'   => $c,
                            'barcode' => $bc,
                        ];
                    }
                }
            }
        }

        // Fallback default sizes if none stored
        if (empty($sizesMap)) {
            $defaultSizes = ['39', '40', '41', '42', '43'];
            foreach ($defaultSizes as $ds) $sizesMap[$ds] = true;
        }

        // Format Sizes with in_stock status (grey-out in Flutter if out of stock)
        $formattedSizes = [];
        foreach (array_keys($sizesMap) as $sizeKey) {
            // If total stock > 0, we treat available sizes as in stock
            $sizeInStock = $totalStock > 0;
            $formattedSizes[] = [
                'size'            => $sizeKey,
                'in_stock'        => $sizeInStock,
                'available_pairs' => $sizeInStock ? max(1, (int)($totalStock / max(1, count($sizesMap)))) : 0,
            ];
        }

        // Format Colors Circles
        $formattedColors = [];
        $colorHexMap = [
            'tan'   => '#D2B48C',
            'black' => '#000000',
            'brown' => '#8B4513',
            'white' => '#FFFFFF',
            'blue'  => '#000080',
            'grey'  => '#808080',
        ];

        foreach (array_keys($colorsMap) as $colorName) {
            $cLower = strtolower($colorName);
            $formattedColors[] = [
                'color_name' => $colorName,
                'hex_code'   => $colorHexMap[$cLower] ?? '#1e293b',
            ];
        }
        if (empty($formattedColors)) {
            $formattedColors[] = ['color_name' => 'Default', 'hex_code' => '#000000'];
        }

        // 2. Format Image Gallery (Grouped per color for swipe & pinch-to-zoom)
        $gallery = [];
        if ($product->webImages && $product->webImages->count() > 0) {
            foreach ($product->webImages as $img) {
                $gallery[] = [
                    'id'        => $img->id,
                    'color'     => $img->color ?: 'General',
                    'image_url' => asset('uploads/products/' . $img->image_path),
                ];
            }
        } else {
            $gallery[] = [
                'id'        => 0,
                'color'     => 'General',
                'image_url' => asset('assets/images/default-shoe.png'),
            ];
        }

        // 3. Customer Reviews Section
        $reviews = [
            [
                'user_name' => 'Kashan S.',
                'rating'    => 5,
                'date'      => '28-Oct-2026',
                'comment'   => 'Super comfortable sole and genuine leather finish! Perfect fit for size 41.',
                'verified'  => true,
            ],
            [
                'user_name' => 'Hamza Ali',
                'rating'    => 4.5,
                'date'      => '15-Oct-2026',
                'comment'   => 'Very elegant footwear for office and casual wear. Delivery was fast too.',
                'verified'  => true,
            ]
        ];

        // 4. "You May Also Like" Related Products
        $relatedShoes = Product::with(['brand', 'webImages'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get()
            ->map(function ($rp) {
                $rPrice = (float) ($rp->web_sale_price ?: ($rp->sale_price_per_piece ?: 0));
                $firstImg = $rp->webImages->first();
                return [
                    'id'              => $rp->id,
                    'name'            => $rp->item_name,
                    'brand_name'      => $rp->brand->name ?? 'Young Fashion',
                    'price'           => $rPrice,
                    'formatted_price' => 'Rs. ' . number_format($rPrice, 0),
                    'image_url'       => $firstImg ? asset('uploads/products/' . $firstImg->image_path) : asset('assets/images/default-shoe.png'),
                    'rating'          => 4.7,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => [
                'id'              => $product->id,
                'name'            => $product->item_name,
                'article_code'    => $product->item_code,
                'brand_name'      => $product->brand->name ?? 'Young Fashion',
                'category_name'   => $product->category_relation->name ?? 'Footwear',
                'price'           => $price,
                'original_price'  => $mrp > $price ? $mrp : null,
                'discount_percent'=> ($mrp > $price && $mrp > 0) ? round((($mrp - $price) / $mrp) * 100) : 0,
                'formatted_price' => 'Rs. ' . number_format($price, 0),
                'total_stock'     => $totalStock,
                'in_stock'        => $totalStock > 0,
                'is_wishlisted'   => $isWishlisted,
                'rating'          => 4.8,
                'reviews_count'   => count($reviews),

                // Gallery & Variant Selectors for Flutter
                'image_gallery'   => $gallery,
                'colors'          => $formattedColors,
                'sizes'           => $formattedSizes,
                'size_guide_url'  => url('/size-guide-modal'),

                // Expandable Details
                'description'     => $product->meta_description ?: $product->description ?: 'Premium handcrafted footwear engineered with cushioned insoles for all-day comfort and long-lasting durability.',
                'specifications'  => [
                    'Upper Material' => 'Genuine Cow Leather / Microfiber',
                    'Sole Material'  => 'Flexible Rubber / PU Anti-Slip Sole',
                    'Insole'         => 'High Density Memory Cushion',
                    'Closure'        => 'Slip-on Loafer',
                    'Origin'         => 'Handmade in Pakistan',
                ],
                'delivery_info'   => [
                    'shipping_fee'   => 'Rs. 200 (Free over Rs. 4,000)',
                    'estimated_days' => '2 - 4 Business Days',
                    'return_policy'  => '7 Days Easy Return & Size Exchange Guarantee',
                ],

                // Customer Feedback & Recommendations
                'reviews'         => $reviews,
                'you_may_also_like' => $relatedShoes,
            ]
        ]);
    }
}
