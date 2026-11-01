<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductDiscount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    // Discount List Page
    public function index()
    {
        $discounts = ProductDiscount::with('product.category_relation', 'product.sub_category_relation', 'product.unit', 'product.brand')
            ->orderByDesc('id')
            ->get();

        return view('admin_panel.product.discount.discount_index', compact('discounts'));
    }

    // Show Create Discount Page
    public function create(Request $request)
    {
        $productIds = $request->products ? array_filter(explode(',', $request->products)) : [];
        if (empty($productIds)) {
            $products = Product::with(['category_relation', 'sub_category_relation', 'unit', 'brand', 'stock', 'discounts'])
                ->where('is_active', 1)
                ->orderBy('item_name')
                ->get();
        } else {
            $products = Product::with(['category_relation', 'sub_category_relation', 'unit', 'brand', 'stock', 'discounts'])
                ->whereIn('id', $productIds)
                ->get();
        }

        return view('admin_panel.product.discount.discount_create', compact('products'));
    }

    // Store Discounts (Supporting both Whole Product & Individual Variants)
    public function store(Request $request)
    {
        $request->validate([
            'product_id.*'          => ['required', 'integer', 'exists:products,id'],
            'discount_percentage.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount.*'     => ['nullable', 'numeric', 'min:0'],
            'date.*'                => ['required', 'date'],
            'status.*'              => ['required', 'in:0,1'],
        ]);

        $savedCount = 0;

        foreach ($request->product_id as $key => $productId) {
            $product = Product::findOrFail($productId);

            $variantRowId   = $request->variant_row_id[$key] ?? null;
            $variantName    = $request->variant_name[$key] ?? null;
            $variantSize    = $request->variant_size[$key] ?? null;
            $variantColor   = $request->variant_color[$key] ?? null;
            $variantBarcode = $request->variant_barcode[$key] ?? null;

            $actualPrice    = (float) ($request->actual_price[$key] ?? $product->price);
            $discountPercentage = (float) ($request->discount_percentage[$key] ?? 0);
            $discountAmount     = (float) ($request->discount_amount[$key] ?? 0);
            $status             = (int) ($request->status[$key] ?? 1);
            $date               = $request->date[$key] ?? now()->toDateString();

            $percDiscount  = round($actualPrice * $discountPercentage / 100, 2);
            $totalDiscount = round($percDiscount + $discountAmount, 2);

            if ($totalDiscount > $actualPrice && $actualPrice > 0) {
                $displayName = $variantName ? "{$product->item_name} [{$variantName}]" : $product->item_name;
                return back()
                    ->withErrors([
                        "discount_percentage.$key" => "Total discount exceeds original price for '{$displayName}'.",
                        "discount_amount.$key"     => "Total discount exceeds original price for '{$displayName}'.",
                    ])
                    ->withInput();
            }

            // Only save row if discount is applied, or if an existing discount is being updated
            if ($totalDiscount <= 0 && empty($request->existing_discount_id[$key])) {
                continue;
            }

            $finalPrice = round(max(0, $actualPrice - $totalDiscount), 2);

            ProductDiscount::updateOrCreate(
                [
                    'product_id'     => $productId,
                    'variant_row_id' => $variantRowId ? $variantRowId : null,
                ],
                [
                    'variant_name'        => $variantName,
                    'variant_size'        => $variantSize,
                    'variant_color'       => $variantColor,
                    'variant_barcode'     => $variantBarcode,
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
            return back()->with('warning', 'Please enter a discount percentage or PKR amount for at least one item or variant.')->withInput();
        }

        return redirect()->route('discount.index')->with('success', "{$savedCount} discount offer(s) saved successfully!");
    }

    // Toggle Status Active/Inactive
    public function toggleStatus($id)
    {
        $discount = ProductDiscount::findOrFail($id);
        $discount->status = !$discount->status;
        $discount->save();

        return redirect()->back()->with('success', 'Discount status updated.');
    }

    // Delete a Discount
    public function destroy($id)
    {
        $discount = ProductDiscount::findOrFail($id);
        $discount->delete();

        return redirect()->back()->with('success', 'Discount offer deleted successfully.');
    }

    // Discount Barcode Page
    public function barcode($id)
    {
        $discount = ProductDiscount::with('product.brand')->findOrFail($id);
        return view('admin_panel.product.discount.discount_barcode', compact('discount'));
    }
}
