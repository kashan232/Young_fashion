<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\ProductVariantStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    protected $variantService;

    public function __construct(ProductVariantStockService $variantService)
    {
        $this->variantService = $variantService;
    }

    public function index()
    {
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product'])
            ->latest()
            ->paginate(20);

        return view('admin_panel.warehouses.stock_transfers.index', compact('transfers'));
    }

    public function create(Request $request)
    {
        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $selectedWarehouseId = $request->query('warehouse_id');
        $selectedProductId = $request->query('product_id');
        $toShop = $request->query('to_shop', false);

        // If product is requested without warehouse, find the warehouse that has stock for it
        if ($selectedProductId && !$selectedWarehouseId) {
            $stockWh = WarehouseStock::where('product_id', $selectedProductId)
                ->where('total_pieces', '>', 0)
                ->orderByDesc('total_pieces')
                ->value('warehouse_id');
            $selectedWarehouseId = $stockWh ?: Warehouse::orderBy('id')->value('id');
        }

        if (!$selectedWarehouseId && $warehouses->isNotEmpty()) {
            $selectedWarehouseId = $warehouses->first()->id;
        }

        // Get warehouse stocks map for selected warehouse
        $warehouseStocks = [];
        if ($selectedWarehouseId) {
            $warehouseStocks = WarehouseStock::where('warehouse_id', $selectedWarehouseId)
                ->pluck('total_pieces', 'product_id')
                ->toArray();
        }

        $products = Product::where('is_active', true)
            ->orderBy('item_name')
            ->get()
            ->map(function ($p) use ($warehouseStocks) {
                $hasVariants = false;
                if (!empty($p->color)) {
                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                    $hasVariants = is_array($parsed) && count($parsed) > 0 && (isset($parsed[0]['name']) || isset($parsed[0]['size']));
                }

                $whPieces = (float)($warehouseStocks[$p->id] ?? 0);
                $p->has_variants = $hasVariants;
                $p->warehouse_pieces = $whPieces;
                $p->is_available = $whPieces > 0;
                return $p;
            })
            ->sortByDesc('warehouse_pieces')
            ->values();

        // Preload variants for selected product if present
        $initialVariants = [];
        if ($selectedProductId && $selectedWarehouseId) {
            $targetProduct = Product::find($selectedProductId);
            if ($targetProduct) {
                $initialVariants = $this->variantService->getVariantsWithStock($targetProduct, (int)$selectedWarehouseId);
            }
        }

        return view('admin_panel.warehouses.stock_transfers.create', compact(
            'warehouses',
            'products',
            'selectedWarehouseId',
            'selectedProductId',
            'toShop',
            'initialVariants'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|integer|exists:warehouses,id',
            'product_id'        => 'required|array',
            'product_id.*'      => 'required|integer|exists:products,id',
            'quantity'          => 'required|array',
            'quantity.*'        => 'required|numeric|min:1',
            'variant_key'       => 'nullable|array',
            'size'              => 'nullable|array',
            'color'             => 'nullable|array',
        ]);

        $fromWarehouse = (int) $request->from_warehouse_id;
        $toWarehouse   = $request->to_warehouse_id ? (int) $request->to_warehouse_id : null;
        $toShop        = $request->to_shop ? true : false;
        $remarks       = $request->remarks;

        if ($fromWarehouse == $toWarehouse && !$toShop) {
            return back()->with('error', 'Source and Destination warehouses cannot be the same.')->withInput();
        }

        if (!$toShop && !$toWarehouse) {
            return back()->with('error', 'Please select a destination warehouse or choose "Transfer to Shop".')->withInput();
        }

        try {
            DB::transaction(function () use ($request, $fromWarehouse, $toWarehouse, $toShop, $remarks) {
                foreach ($request->product_id as $index => $productId) {
                    $qty = (float) ($request->quantity[$index] ?? 0);
                    if ($qty <= 0) continue;

                    $product = Product::findOrFail($productId);
                    $size = $request->size[$index] ?? null;
                    $color = $request->color[$index] ?? null;
                    $variantKey = $request->variant_key[$index] ?? null;

                    // If variant is specified, validate variant-specific stock in source warehouse
                    if (!empty($size) && $size !== '-' || !empty($color) && $color !== '-') {
                        $variants = $this->variantService->getVariantsWithStock($product, $fromWarehouse);
                        $matched = collect($variants)->first(function($v) use ($size, $color, $variantKey) {
                            if ($variantKey && $v['variant_key'] === $variantKey) return true;
                            return strtolower(trim($v['size'])) === strtolower(trim($size)) &&
                                   strtolower(trim($v['color'])) === strtolower(trim($color));
                        });

                        if ($matched && $matched['current_stock'] < $qty) {
                            $avail = $matched['current_stock'];
                            throw new \Exception("Insufficient stock for {$product->item_name} (Size: {$size}, Color: {$color}) in source warehouse. Available: {$avail}, Requested: {$qty}");
                        }
                    }

                    // Source WarehouseStock aggregate validation & lock
                    $sourceStock = WarehouseStock::where('warehouse_id', $fromWarehouse)
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    if (!$sourceStock || (float)$sourceStock->total_pieces < $qty) {
                        $avail = $sourceStock ? (float)$sourceStock->total_pieces : 0;
                        throw new \Exception("Insufficient stock for {$product->item_name} in source warehouse. Available: {$avail}, Requested: {$qty}");
                    }

                    // Deduct from Source WarehouseStock
                    $ppb = $product->pieces_per_box > 0 ? (float)$product->pieces_per_box : 1.0;
                    $sourceStock->total_pieces = max(0, (float)$sourceStock->total_pieces - $qty);
                    $sourceStock->quantity = $sourceStock->total_pieces / $ppb;
                    $sourceStock->save();

                    // Add to Destination WarehouseStock
                    if (!$toShop && $toWarehouse) {
                        $destStock = WarehouseStock::firstOrCreate(
                            [
                                'warehouse_id' => $toWarehouse,
                                'product_id'   => $productId,
                            ],
                            [
                                'quantity'     => 0,
                                'total_pieces' => 0,
                                'remarks'      => 'Transferred from WH #' . $fromWarehouse,
                            ]
                        );
                        $destStock->total_pieces = (float)$destStock->total_pieces + $qty;
                        $destStock->quantity = $destStock->total_pieces / $ppb;
                        $destStock->save();
                    }

                    // Create StockTransfer Record with Variant metadata
                    $transfer = StockTransfer::create([
                        'from_warehouse_id' => $fromWarehouse,
                        'to_warehouse_id'   => $toShop ? null : $toWarehouse,
                        'to_shop'           => $toShop,
                        'product_id'        => $productId,
                        'size'              => $size ?: null,
                        'color'             => $color ?: null,
                        'variant_data'      => $variantKey ?: ($size ? "Size: {$size}, Color: {$color}" : null),
                        'quantity'          => $qty,
                        'remarks'           => $remarks,
                    ]);

                    $variantLabel = ($size || $color) ? " | Variant: Size: {$size}, Color: {$color}" : "";

                    // Log Stock Movement OUT
                    DB::table('stock_movements')->insert([
                        'product_id' => $productId,
                        'type'       => 'out',
                        'qty'        => -1 * abs($qty),
                        'ref_type'   => 'TRANSFER_OUT',
                        'ref_id'     => $transfer->id,
                        'note'       => "Warehouse #{$fromWarehouse} | Transfer to " . ($toShop ? 'Shop' : "Warehouse #{$toWarehouse}") . $variantLabel,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Log Stock Movement IN
                    if (!$toShop && $toWarehouse) {
                        DB::table('stock_movements')->insert([
                            'product_id' => $productId,
                            'type'       => 'in',
                            'qty'        => abs($qty),
                            'ref_type'   => 'TRANSFER_IN',
                            'ref_id'     => $transfer->id,
                            'note'       => "Warehouse #{$toWarehouse} | Transfer from Warehouse #{$fromWarehouse}" . $variantLabel,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Transfer Failed: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('stock_transfers.index')->with('success', 'Stock transferred successfully.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        return back()->with('error', 'Deleting transfers not allowed.');
    }

    public function getStockQuantity(Request $request)
    {
        $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        return response()->json([
            'quantity' => $stock ? (float)$stock->total_pieces : 0,
        ]);
    }

    public function getProductVariants(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $warehouseId = (int)$request->warehouse_id;

        $variants = $this->variantService->getVariantsWithStock($product, $warehouseId);

        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $product->id)
            ->first();

        return response()->json([
            'product_id'   => $product->id,
            'product_name' => $product->item_name,
            'item_code'    => $product->item_code,
            'image'        => $product->image ? asset('uploads/products/'.$product->image) : null,
            'has_variants' => count($variants) > 0,
            'variants'     => $variants,
            'total_pieces' => $stock ? (float)$stock->total_pieces : 0,
        ]);
    }

    public function getProductsByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        if (!$warehouseId) {
            return response()->json([]);
        }

        $warehouseStocks = WarehouseStock::where('warehouse_id', $warehouseId)
            ->pluck('total_pieces', 'product_id');

        $products = Product::where('is_active', true)
            ->orderBy('item_name')
            ->get()
            ->map(function ($p) use ($warehouseStocks) {
                $hasVariants = false;
                if (!empty($p->color)) {
                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                    $hasVariants = is_array($parsed) && count($parsed) > 0 && (isset($parsed[0]['name']) || isset($parsed[0]['size']));
                }

                $whPieces = (float)($warehouseStocks[$p->id] ?? 0);

                return [
                    'id'           => $p->id,
                    'item_name'    => $p->item_name,
                    'item_code'    => $p->item_code,
                    'has_variants' => $hasVariants,
                    'total_pieces' => $whPieces,
                    'is_available' => $whPieces > 0,
                ];
            })
            ->sortByDesc('total_pieces')
            ->values();

        return response()->json($products);
    }
}
