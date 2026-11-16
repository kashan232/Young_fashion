<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\Unit;
use Illuminate\Http\Request;

class FlutterCatalogController extends Controller
{
    /**
     * Helper to build absolute asset URL
     */
    private function assetUrl($path)
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset($path);
    }

    /**
     * 1. GET /api/v1/flutter/catalog/products
     * Full Products Catalog with Filtering, Sorting & Pagination
     */
    public function products(Request $request)
    {
        $query = Product::with(['category_relation', 'sub_category_relation', 'brand', 'unit', 'activeDiscount'])
            ->withSum('warehouseStocks as total_stock', 'total_pieces')
            ->whereNull('deleted_at');

        // Optional filter: is_active (default all or active)
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active') ? 1 : 0);
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter: Subcategory
        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // Filter: Brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter: Search Keyword
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(function ($q) use ($kw) {
                $q->where('item_name', 'like', "%{$kw}%")
                  ->orWhere('item_code', 'like', "%{$kw}%")
                  ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', "%{$kw}%"))
                  ->orWhereHas('category_relation', fn($cq) => $cq->where('name', 'like', "%{$kw}%"));
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_low_high':
                $query->orderBy('sale_price_per_piece', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('sale_price_per_piece', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('item_name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $perPage = (int) $request->get('per_page', 20);
        $paginated = $query->paginate($perPage);

        $items = collect($paginated->items())->map(function ($product) {
            $variants = $product->parsed_variants;
            $salePrice = (float) ($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0);
            
            // Check active discount
            $discount = $product->activeDiscount;
            $discountPercent = $discount ? (float) $discount->discount_percentage : 0;
            $discountAmount = $discount ? (float) $discount->discount_amount : 0;
            $finalPrice = $discount ? (float) $discount->final_price : $salePrice;

            // Image URL
            $imageUrl = null;
            if (!empty($product->image)) {
                $imageUrl = $this->assetUrl('images/' . $product->image);
            } elseif (!empty($product->web_main_image)) {
                $imageUrl = $this->assetUrl('images/' . $product->web_main_image);
            }

            return [
                'id'                  => $product->id,
                'item_code'           => $product->item_code,
                'item_name'           => $product->item_name,
                'category_id'         => $product->category_id,
                'category_name'       => $product->category_relation->name ?? null,
                'sub_category_id'     => $product->sub_category_id,
                'sub_category_name'   => $product->sub_category_relation->name ?? null,
                'brand_id'            => $product->brand_id,
                'brand_name'          => $product->brand->name ?? null,
                'unit_id'             => $product->unit_id,
                'unit_name'           => $product->unit->name ?? null,
                'regular_price'       => $salePrice,
                'final_price'         => $finalPrice,
                'has_discount'        => $discount ? true : false,
                'discount_percentage' => $discountPercent,
                'discount_amount'     => $discountAmount,
                'stock_quantity'      => (int) ($product->total_stock ?? $product->boxes_quantity ?? 0),
                'in_stock'            => ((int) ($product->total_stock ?? $product->boxes_quantity ?? 0)) > 0,
                'image_url'           => $imageUrl,
                'is_active'           => (bool) $product->is_active,
                'variants_count'      => count($variants),
            ];
        });

        return response()->json([
            'status'     => true,
            'message'    => 'Products fetched successfully',
            'data'       => $items,
            'pagination' => [
                'total'        => $paginated->total(),
                'per_page'     => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
            ]
        ]);
    }

    /**
     * 1b. GET /api/v1/flutter/catalog/products/{id}
     * Single Product Details with All Variants
     */
    public function productDetails($id)
    {
        $product = Product::with(['category_relation', 'sub_category_relation', 'brand', 'unit', 'webImages', 'activeDiscounts'])
            ->withSum('warehouseStocks as total_stock', 'total_pieces')
            ->whereNull('deleted_at')
            ->findOrFail($id);

        $variants = $product->parsed_variants;
        $salePrice = (float) ($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0);

        // Map web images
        $gallery = [];
        if (!empty($product->image)) {
            $gallery[] = $this->assetUrl('images/' . $product->image);
        }
        foreach ($product->webImages as $img) {
            if (!empty($img->image_path)) {
                $gallery[] = $this->assetUrl('images/' . $img->image_path);
            }
        }

        // Map variants with their images, stock, and individual discount
        $mappedVariants = array_map(function ($v) use ($salePrice, $product) {
            $vPrice = isset($v['sale_price']) && (float)$v['sale_price'] > 0 ? (float)$v['sale_price'] : $salePrice;
            
            // Check discount for this specific variant row_id
            $vDiscount = $product->activeDiscounts->firstWhere('variant_row_id', $v['row_id'] ?? '');
            $vFinalPrice = $vDiscount ? (float) $vDiscount->final_price : $vPrice;

            $vImages = [];
            if (!empty($v['images']) && is_array($v['images'])) {
                foreach ($v['images'] as $imgName) {
                    $vImages[] = $this->assetUrl('images/' . $imgName);
                }
            }

            return [
                'row_id'              => $v['row_id'] ?? null,
                'name'                => $v['name'] ?? null,
                'size'                => $v['size'] ?? null,
                'color'               => $v['color'] ?? null,
                'barcode'             => $v['barcode'] ?? null,
                'stock'               => (int) ($v['stock'] ?? 0),
                'in_stock'            => ((int) ($v['stock'] ?? 0)) > 0,
                'regular_price'       => $vPrice,
                'final_price'         => $vFinalPrice,
                'has_discount'        => $vDiscount ? true : false,
                'discount_percentage' => $vDiscount ? (float) $vDiscount->discount_percentage : 0,
                'images'              => $vImages,
                'is_base'             => !empty($v['is_base_variant']),
            ];
        }, $variants);

        // Unique sizes and colors list
        $availableSizes = array_values(array_unique(array_filter(array_column($variants, 'size'))));
        $availableColors = array_values(array_unique(array_filter(array_column($variants, 'color'))));

        return response()->json([
            'status'  => true,
            'message' => 'Product details fetched successfully',
            'data'    => [
                'id'                  => $product->id,
                'item_code'           => $product->item_code,
                'item_name'           => $product->item_name,
                'category'            => [
                    'id'   => $product->category_id,
                    'name' => $product->category_relation->name ?? null,
                ],
                'sub_category'        => [
                    'id'   => $product->sub_category_id,
                    'name' => $product->sub_category_relation->name ?? null,
                ],
                'brand'               => [
                    'id'   => $product->brand_id,
                    'name' => $product->brand->name ?? null,
                ],
                'unit'                => [
                    'id'   => $product->unit_id,
                    'name' => $product->unit->name ?? null,
                ],
                'regular_price'       => $salePrice,
                'stock_quantity'      => (int) ($product->total_stock ?? $product->boxes_quantity ?? 0),
                'in_stock'            => ((int) ($product->total_stock ?? $product->boxes_quantity ?? 0)) > 0,
                'description'         => $product->meta_description ?? 'Authentic premium footwear crafted with precision comfort.',
                'main_image'          => !empty($product->image) ? $this->assetUrl('images/' . $product->image) : null,
                'gallery_images'      => array_values(array_unique($gallery)),
                'available_sizes'     => $availableSizes,
                'available_colors'    => $availableColors,
                'variants'            => $mappedVariants,
            ]
        ]);
    }

    /**
     * 2. GET /api/v1/flutter/catalog/discounts
     * Active & Scheduled Product Discounts
     */
    public function discounts(Request $request)
    {
        $query = ProductDiscount::with(['product' => function ($q) {
            $q->select('id', 'item_name', 'item_code', 'image', 'brand_id', 'category_id')
              ->with(['brand:id,name', 'category_relation:id,name']);
        }]);

        // Default only active discounts unless status specified
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 1);
        }

        $discounts = $query->orderBy('id', 'desc')->get();

        $data = $discounts->map(function ($d) {
            $productImg = null;
            if (!empty($d->product->image)) {
                $productImg = $this->assetUrl('images/' . $d->product->image);
            }

            return [
                'id'                  => $d->id,
                'product_id'          => $d->product_id,
                'product_name'        => $d->product->item_name ?? '—',
                'product_code'        => $d->product->item_code ?? '—',
                'brand_name'          => $d->product->brand->name ?? null,
                'category_name'       => $d->product->category_relation->name ?? null,
                'product_image'       => $productImg,
                'variant_row_id'      => $d->variant_row_id,
                'variant_name'        => $d->variant_name,
                'variant_size'        => $d->variant_size,
                'variant_color'       => $d->variant_color,
                'variant_barcode'     => $d->variant_barcode,
                'actual_price'        => (float) $d->actual_price,
                'discount_percentage' => (float) $d->discount_percentage,
                'discount_amount'     => (float) $d->discount_amount,
                'final_price'         => (float) $d->final_price,
                'valid_until_date'    => $d->date,
                'is_active'           => (bool) $d->status,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Discounts fetched successfully',
            'count'   => $data->count(),
            'data'    => $data,
        ]);
    }

    /**
     * 3. GET /api/v1/flutter/catalog/categories
     * All Categories with Subcategories Count & Image
     */
    public function categories()
    {
        $categories = Category::withCount('subcategories')->get();

        $data = $categories->map(function ($c) {
            $img = null;
            if (!empty($c->web_image)) {
                $img = $this->assetUrl('images/' . $c->web_image);
            }

            return [
                'id'                  => $c->id,
                'name'                => $c->name,
                'image_url'           => $img,
                'subcategories_count' => $c->subcategories_count,
                'show_on_website'     => (bool) $c->show_on_website,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Categories fetched successfully',
            'count'   => $data->count(),
            'data'    => $data,
        ]);
    }

    /**
     * 4. GET /api/v1/flutter/catalog/subcategories
     * Subcategories with Category Filter
     */
    public function subcategories(Request $request)
    {
        $query = Subcategory::with('category:id,name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $subcategories = $query->orderBy('name', 'asc')->get();

        $data = $subcategories->map(function ($s) {
            return [
                'id'            => $s->id,
                'category_id'   => $s->category_id,
                'category_name' => $s->category->name ?? null,
                'name'          => $s->name,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Subcategories fetched successfully',
            'count'   => $data->count(),
            'data'    => $data,
        ]);
    }

    /**
     * 5. GET /api/v1/flutter/catalog/brands
     * Brands List with Product Count
     */
    public function brands()
    {
        $brands = Brand::withCount('products')->orderBy('name', 'asc')->get();

        $data = $brands->map(function ($b) {
            return [
                'id'             => $b->id,
                'name'           => $b->name,
                'products_count' => $b->products_count,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Brands fetched successfully',
            'count'   => $data->count(),
            'data'    => $data,
        ]);
    }

    /**
     * 6. GET /api/v1/flutter/catalog/units
     * Units List (e.g. Pair, Piece, Box)
     */
    public function units()
    {
        $units = Unit::select('id', 'name')->orderBy('id', 'asc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Units fetched successfully',
            'count'   => $units->count(),
            'data'    => $units,
        ]);
    }

    /**
     * 7. GET /api/v1/flutter/catalog/sizes
     * Sizes List (e.g. 38, 39, 40, EU-38)
     */
    public function sizes()
    {
        $sizes = Size::select('id', 'name', 'size_code')->orderBy('id', 'asc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Sizes fetched successfully',
            'count'   => $sizes->count(),
            'data'    => $sizes,
        ]);
    }

    /**
     * 8. GET /api/v1/flutter/catalog/variants-color
     * Color Variants (Colors List with Hex Code)
     */
    public function colors()
    {
        $colors = Color::select('id', 'name', 'color_code')->orderBy('id', 'asc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Colors fetched successfully',
            'count'   => $colors->count(),
            'data'    => $colors,
        ]);
    }

    /**
     * Bonus: GET /api/v1/flutter/catalog/metadata
     * Fast All-in-One Metadata for Flutter App Initialization
     */
    public function allMetadata()
    {
        return response()->json([
            'status'  => true,
            'message' => 'All catalog metadata fetched successfully',
            'data'    => [
                'categories'    => Category::select('id', 'name')->get(),
                'subcategories' => Subcategory::select('id', 'category_id', 'name')->get(),
                'brands'        => Brand::select('id', 'name')->get(),
                'units'         => Unit::select('id', 'name')->get(),
                'sizes'         => Size::select('id', 'name', 'size_code')->get(),
                'colors'        => Color::select('id', 'name', 'color_code')->get(),
            ]
        ]);
    }
}
