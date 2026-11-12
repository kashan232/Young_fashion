<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDiscount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    // Discount List Page (Products Only - 1 row per product)
    public function index()
    {
        $discounts = ProductDiscount::with([
                'product.category_relation',
                'product.sub_category_relation',
                'product.unit',
                'product.brand',
            ])
            ->whereHas('product')
            ->orderByRaw('CASE WHEN variant_row_id IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('id')
            ->get()
            ->unique('product_id')
            ->values();

        return view('admin_panel.product.discount.discount_index', compact('discounts'));
    }

    // Show Create Discount Page (Products Only - No Variants)
    public function create(Request $request)
    {
        $productIds = $request->products ? array_filter(explode(',', $request->products)) : [];
        $perPage = (int) $request->input('per_page', 50);
        if (!in_array($perPage, [25, 50, 100], true)) {
            $perPage = 50;
        }

        $query = Product::with(['category_relation', 'sub_category_relation', 'unit', 'brand', 'discounts'])
            ->withSum('warehouseStocks', 'total_pieces');

        if (!empty($productIds)) {
            $query->whereIn('id', $productIds);
        } else {
            $query->where('is_active', 1);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('item_name', 'like', "%{$s}%")
                  ->orWhere('item_code', 'like', "%{$s}%")
                  ->orWhere('barcode_path', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->input('discount_filter') === 'with_discount') {
            $query->whereHas('discounts');
        } elseif ($request->input('discount_filter') === 'without_discount') {
            $query->whereDoesntHave('discounts');
        }

        $products = $query->orderBy('item_name')
            ->paginate($perPage)
            ->appends($request->query());

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands = Brand::orderBy('name')->get(['id', 'name']);

        return view('admin_panel.product.discount.discount_create', compact('products', 'categories', 'brands'));
    }

    // Store Product-Level Discounts (No per-variant rules)
    public function store(Request $request)
    {
        $request->validate([
            'product_id'            => ['required', 'array'],
            'product_id.*'          => ['required', 'integer', 'exists:products,id'],
            'discount_percentage.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount.*'     => ['nullable', 'numeric', 'min:0'],
            'date.*'                => ['required', 'date'],
            'status.*'              => ['required', 'in:0,1'],
        ]);

        $savedCount = 0;

        foreach ($request->product_id as $key => $productId) {
            $product = Product::findOrFail($productId);

            $actualPrice = (float) ($request->actual_price[$key] ?? $product->price);
            $discountPercentage = (float) ($request->discount_percentage[$key] ?? 0);
            $discountAmount     = (float) ($request->discount_amount[$key] ?? 0);
            $status             = (int) ($request->status[$key] ?? 1);
            $date               = $request->date[$key] ?? now()->toDateString();

            // Discount % and PKR are alternate/synchronized representations of the SAME discount.
            if ($discountPercentage > 0) {
                $totalDiscount = round($actualPrice * $discountPercentage / 100, 2);
                $discountAmount = $totalDiscount;
            } elseif ($discountAmount > 0) {
                $totalDiscount = min($actualPrice, $discountAmount);
                $discountPercentage = $actualPrice > 0 ? round(($totalDiscount / $actualPrice) * 100, 2) : 0;
            } else {
                $totalDiscount = 0;
                $discountAmount = 0;
                $discountPercentage = 0;
            }

            if ($totalDiscount > $actualPrice && $actualPrice > 0) {
                return back()
                    ->withErrors([
                        "discount_percentage.$key" => "Total discount exceeds original price for '{$product->item_name}'.",
                        "discount_amount.$key"     => "Total discount exceeds original price for '{$product->item_name}'.",
                    ])
                    ->withInput();
            }

            // Only save row if discount is applied, or if an existing discount is being updated
            if ($totalDiscount <= 0 && empty($request->existing_discount_id[$key])) {
                continue;
            }

            // Clean up any legacy variant-level discount rows for this product
            ProductDiscount::where('product_id', $productId)
                ->whereNotNull('variant_row_id')
                ->delete();

            if ($totalDiscount <= 0 && !empty($request->existing_discount_id[$key])) {
                ProductDiscount::where('product_id', $productId)->delete();
                $savedCount++;
                continue;
            }

            $finalPrice = round(max(0, $actualPrice - $totalDiscount), 2);

            ProductDiscount::updateOrCreate(
                [
                    'product_id'     => $productId,
                    'variant_row_id' => null,
                ],
                [
                    'variant_name'        => null,
                    'variant_size'        => null,
                    'variant_color'       => null,
                    'variant_barcode'     => null,
                    'actual_price'        => $actualPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount'     => $discountAmount,
                    'total_discount'      => $totalDiscount,
                    'final_price'         => $finalPrice,
                    'date'                => $date,
                    'status'              => $status,
                ]
            );
            $savedCount++;
        }

        if ($savedCount === 0) {
            return back()->with('warning', 'Please enter a discount percentage or PKR amount for at least one product.')->withInput();
        }

        return redirect()->route('discount.index')->with('success', "{$savedCount} product discount offer(s) saved successfully!");
    }

    // Toggle Status Active/Inactive (applies to product's discount offer)
    public function toggleStatus($id)
    {
        $discount = ProductDiscount::findOrFail($id);
        $newStatus = $discount->status ? 0 : 1;
        ProductDiscount::where('product_id', $discount->product_id)->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Product discount status updated.');
    }

    // Delete a Product Discount
    public function destroy($id)
    {
        $discount = ProductDiscount::findOrFail($id);
        ProductDiscount::where('product_id', $discount->product_id)->delete();

        return redirect()->back()->with('success', 'Product discount offer deleted successfully.');
    }

    // Discount Barcode Page
    public function barcode($id)
    {
        $discount = ProductDiscount::with('product.brand')->findOrFail($id);
        return view('admin_panel.product.discount.discount_barcode', compact('discount'));
    }
}
