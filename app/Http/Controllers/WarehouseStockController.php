<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\ProductVariantStockService;
use App\Services\StockEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseStockController extends Controller
{
    protected $stockService;
    protected $variantService;

    public function __construct(StockEntryService $stockService, ProductVariantStockService $variantService)
    {
        $this->stockService = $stockService;
        $this->variantService = $variantService;
    }

    public function getByWarehouse($warehouseId)
    {
        $products = WarehouseStock::with('product')
            ->where('warehouse_id', $warehouseId)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->product->id,
                    'name' => $row->product->item_name,
                    'qty' => $row->total_pieces,
                ];
            });

        return response()->json($products);
    }

    public function searchWarehouses(Request $request)
    {
        $term = $request->get('q', '');

        $warehouses = Warehouse::query()
            ->select('id', 'warehouse_name')
            ->when($term, function ($query) use ($term) {
                $query->where('warehouse_name', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get();

        return response()->json($warehouses->map(function ($w) {
            return [
                'id' => $w->id,
                'text' => $w->warehouse_name,
            ];
        }));
    }

    public function index()
    {
        $stocks = WarehouseStock::with(['warehouse', 'product'])->latest()->paginate(15);

        // Pre-check which products have variants so UI can show variant badge
        foreach ($stocks as $stock) {
            $stock->has_variants = false;
            $stock->variant_count = 0;
            $stock->variant_sizes = [];

            if ($stock->product && !empty($stock->product->color)) {
                $parsed = is_string($stock->product->color) ? json_decode($stock->product->color, true) : $stock->product->color;
                if (is_array($parsed) && count($parsed) > 0 && (isset($parsed[0]['name']) || isset($parsed[0]['size']))) {
                    $stock->has_variants = true;
                    $stock->variant_count = count($parsed);
                    $sizes = collect($parsed)->pluck('size')->filter(fn($s) => $s !== '-' && !empty($s))->unique()->values()->toArray();
                    $stock->variant_sizes = $sizes;
                }
            }
        }

        return view('admin_panel.warehouses.warehouse_stocks.index', compact('stocks'));
    }

    public function show($id)
    {
        return redirect()->route('warehouse_stocks.index');
    }

    public function getProductVariants(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $warehouseId = $request->query('warehouse_id') ? (int) $request->query('warehouse_id') : null;

        $variants = $this->variantService->getVariantsWithStock($product, $warehouseId);

        $stock = null;
        if ($warehouseId) {
            $stock = WarehouseStock::where('warehouse_id', $warehouseId)->where('product_id', $productId)->first();
        }

        return response()->json([
            'success'      => true,
            'product_id'   => $product->id,
            'product_name' => $product->item_name,
            'item_code'    => $product->item_code,
            'image'        => $product->image ? asset('uploads/products/'.$product->image) : null,
            'has_variants' => count($variants) > 0,
            'variants'     => $variants,
            'total_pieces' => $stock ? (float)$stock->total_pieces : 0,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'       => 'required|exists:warehouses,id',
            'product_id'         => 'required|exists:products,id',
            'total_pieces'       => 'required|numeric|min:0',
            'total_box'          => 'nullable|numeric|min:0',
            'remarks'            => 'nullable|string|max:255',
            'variant_quantities' => 'nullable|array',
        ]);

        try {
            DB::transaction(function() use ($validated, $request) {
                $warehouseId = (int)$validated['warehouse_id'];
                $productId   = (int)$validated['product_id'];
                $totalPieces = (float)$validated['total_pieces'];
                $totalBox    = (int)($validated['total_box'] ?? 0);
                $remarks     = $request->input('remarks');

                // If variant quantities were entered, log adjustment per variant
                if ($request->has('variant_quantities') && is_array($request->variant_quantities)) {
                    foreach ($request->variant_quantities as $vKey => $vQty) {
                        $vQty = (float)$vQty;
                        if ($vQty > 0) {
                            $parts = explode('|', $vKey);
                            $vName  = $parts[0] ?? '';
                            $vSize  = $parts[1] ?? '-';
                            $vColor = $parts[2] ?? '-';

                            DB::table('stock_movements')->insert([
                                'product_id' => $productId,
                                'type'       => 'adjustment',
                                'qty'        => $vQty,
                                'ref_type'   => 'WAREHOUSE_STOCK_ADD',
                                'ref_id'     => null,
                                'note'       => "Warehouse #{$warehouseId} | Variant: {$vName} (Size: {$vSize}, Color: {$vColor}) | Add Stock via Modal",
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                $this->stockService->addStock(
                    $warehouseId,
                    $productId,
                    $totalPieces,
                    $totalBox,
                    $remarks
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: '.$e->getMessage(),
            ], 500);
        }
    }

    public function editData($id)
    {
        $stock = WarehouseStock::with(['product', 'warehouse'])->findOrFail($id);
        $piecesPerBox = $stock->product->pieces_per_box ?? 0;

        $boxes = 0;
        $loose = $stock->total_pieces;

        if ($piecesPerBox > 0) {
            $boxes = floor($stock->total_pieces / $piecesPerBox);
            $loose = $stock->total_pieces % $piecesPerBox;
        }

        $variants = $this->variantService->getVariantsWithStock($stock->product, $stock->warehouse_id);

        return response()->json([
            'id'             => $stock->id,
            'warehouse_id'   => $stock->warehouse_id,
            'warehouse_name' => $stock->warehouse->warehouse_name ?? '',
            'product_id'     => $stock->product_id,
            'product_name'   => $stock->product->item_name,
            'product_code'   => $stock->product->item_code,
            'pieces_per_box' => $piecesPerBox,
            'total_pieces'   => $stock->total_pieces,
            'image'          => $stock->product->image ? asset('uploads/products/'.$stock->product->image) : null,
            'remarks'        => $stock->remarks,
            'has_variants'   => count($variants) > 0,
            'variants'       => $variants,
        ]);
    }

    public function update(Request $request, $id)
    {
        $warehouseStock = WarehouseStock::findOrFail($id);

        $request->validate([
            'total_pieces' => 'required|numeric|min:0',
            'remarks'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $warehouseStock) {
            $oldQty = $warehouseStock->total_pieces;
            $newQty = (float)$request->total_pieces;
            $delta = $newQty - $oldQty;

            if ($delta != 0) {
                $warehouseStock->total_pieces = $newQty;
                $ppb = $warehouseStock->product->pieces_per_box > 0 ? $warehouseStock->product->pieces_per_box : 1;
                $warehouseStock->quantity = $newQty / $ppb;
                $warehouseStock->remarks = $request->remarks;
                $warehouseStock->save();

                DB::table('stock_movements')->insert([
                    'product_id'   => $warehouseStock->product_id,
                    'type'         => 'adjustment',
                    'qty'          => $delta,
                    'ref_type'     => 'MANUAL_EDIT_MODAL',
                    'ref_id'       => $warehouseStock->id,
                    'note'         => "Warehouse #{$warehouseStock->warehouse_id} | Manual Stock Edit via Modal",
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully!',
        ]);
    }

    public function destroy(WarehouseStock $warehouseStock)
    {
        $warehouseStock->delete();
        return back()->with('success', 'Stock deleted successfully.');
    }

    public function searchProducts(Request $request)
    {
        $term = $request->get('q', '');

        $products = Product::query()
            ->select('id', 'item_name', 'item_code', 'pieces_per_box', 'image', 'color')
            ->when($term, function ($query) use ($term) {
                $query->where('item_name', 'like', "%{$term}%")
                    ->orWhere('item_code', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products->map(function ($p) {
            $hasVariants = false;
            if (!empty($p->color)) {
                $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                $hasVariants = is_array($parsed) && count($parsed) > 0 && (isset($parsed[0]['name']) || isset($parsed[0]['size']));
            }
            return [
                'id'             => $p->id,
                'text'           => "{$p->item_code} - {$p->item_name}",
                'item_name'      => $p->item_name,
                'item_code'      => $p->item_code,
                'pieces_per_box' => $p->pieces_per_box ?? 0,
                'has_variants'   => $hasVariants,
                'image'          => $p->image ? asset('uploads/products/'.$p->image) : null,
            ];
        }));
    }

    public function getWarehouseStock(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
        ]);

        $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        $product = Product::find($request->product_id);
        $variants = $this->variantService->getVariantsWithStock($product, (int)$request->warehouse_id);

        return response()->json([
            'total_pieces' => $stock ? (float)$stock->total_pieces : 0,
            'has_variants' => count($variants) > 0,
            'variants'     => $variants,
        ]);
    }
}
