<?php

namespace App\Services;

use App\Models\Product;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class ProductVariantStockService
{
    /**
     * Compute live variant stock balances for a product, optionally filtered by warehouse.
     *
     * @param Product $product
     * @param int|null $warehouseId
     * @return array
     */
    public function getVariantsWithStock(Product $product, ?int $warehouseId = null): array
    {
        $rawColor = $product->color;
        if (empty($rawColor)) {
            return [];
        }

        try {
            $parsed = is_string($rawColor) ? json_decode($rawColor, true) : $rawColor;
            if (!is_array($parsed) || count($parsed) === 0 || (!isset($parsed[0]['name']) && !isset($parsed[0]['size']) && !isset($parsed[0]['color']))) {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }

        // Primary warehouse where product was initially created
        $initialWhId = WarehouseStock::where('product_id', $product->id)->orderBy('id')->value('warehouse_id');

        // 1. Fetch Sales
        $salesQuery = DB::table('sale_items')->where('product_id', $product->id);
        if ($warehouseId !== null) {
            $salesQuery->where('warehouse_id', $warehouseId);
        }
        $salesList = $salesQuery->select('total_pieces', 'color')->get();

        // 2. Fetch Confirmed Web Sales (deducted from primary warehouse)
        if ($warehouseId === null || ($initialWhId && $warehouseId == $initialWhId)) {
            $webSalesList = DB::table('ecommerce_order_items as eoi')
                ->join('ecommerce_orders as eo', 'eo.id', '=', 'eoi.ecommerce_order_id')
                ->where('eoi.product_id', $product->id)
                ->where('eo.is_stock_deducted', 1)
                ->select('eoi.quantity as total_pieces', 'eoi.color', 'eoi.size')
                ->get();

            $salesListArray = $salesList->toArray();
            foreach ($webSalesList as $wItem) {
                $salesListArray[] = (object) [
                    'total_pieces' => $wItem->total_pieces,
                    'color' => json_encode([
                        'color' => $wItem->color ?: '-',
                        'size'  => $wItem->size ?: '-'
                    ])
                ];
            }
            $salesList = collect($salesListArray);
        }

        // 3. Fetch Sale Returns
        $returnsQuery = DB::table('sale_return_items as sri')
            ->join('sale_returns as sr', 'sr.id', '=', 'sri.sale_return_id')
            ->where('sri.product_id', $product->id);

        if ($warehouseId !== null) {
            $returnsQuery->where(function($q) use ($warehouseId) {
                $q->where('sri.warehouse_id', $warehouseId)
                  ->orWhere('sr.warehouse_id', $warehouseId);
            });
        }
        $returnsList = $returnsQuery->select('sri.qty', 'sri.color', 'sr.sale_id')->get();

        // Preload sale colors if return items don't have direct color
        $saleIds = $returnsList->pluck('sale_id')->unique()->filter()->toArray();
        $saleItemsMap = [];
        if (!empty($saleIds)) {
            $siList = DB::table('sale_items')
                ->whereIn('sale_id', $saleIds)
                ->where('product_id', $product->id)
                ->select('sale_id', 'color')
                ->get();
            foreach ($siList as $si) {
                $saleItemsMap[$si->sale_id][] = $si->color;
            }
        }

        // 4. Fetch Purchases
        $purchasesQuery = DB::table('purchase_items as pi')
            ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
            ->where('pi.product_id', $product->id)
            ->whereIn('pur.status_purchase', ['approved', 'Returned', 'Partial']);

        if ($warehouseId !== null) {
            $purchasesQuery->where('pur.warehouse_id', $warehouseId);
        }
        $purchasesList = $purchasesQuery->select('pi.qty as total_pieces', 'pi.color')->get();

        // 5. Fetch Purchase Returns
        $prQuery = DB::table('purchase_return_items as pri')
            ->join('purchase_returns as pr', 'pr.id', '=', 'pri.purchase_return_id')
            ->where('pri.product_id', $product->id);

        if ($warehouseId !== null) {
            $prQuery->where('pr.warehouse_id', $warehouseId);
        }
        $purchaseReturnsList = $prQuery->select('pri.qty', 'pri.color')->get();

        // 6. Fetch Stock Transfers IN & OUT
        $transfersIn = collect();
        $transfersOut = collect();
        if ($warehouseId !== null) {
            $transfersIn = DB::table('stock_transfers')
                ->where('product_id', $product->id)
                ->where('to_warehouse_id', $warehouseId)
                ->select('quantity', 'color', 'size', 'variant_data')
                ->get();

            $transfersOut = DB::table('stock_transfers')
                ->where('product_id', $product->id)
                ->where('from_warehouse_id', $warehouseId)
                ->select('quantity', 'color', 'size', 'variant_data')
                ->get();
        } else {
            // Overall across warehouses: transfers to shop reduce warehouse stock
            $transfersOut = DB::table('stock_transfers')
                ->where('product_id', $product->id)
                ->where('to_shop', 1)
                ->select('quantity', 'color', 'size', 'variant_data')
                ->get();
        }

        // 7. Fetch Stock Adjustments
        $adjQuery = DB::table('stock_movements')
            ->where('product_id', $product->id)
            ->where('type', 'adjustment')
            ->whereNotIn('ref_type', ['INIT']);

        $adjList = $adjQuery->select('qty', 'note')->get();

        $results = [];
        foreach ($parsed as $v) {
            $vName = $v['name'] ?? $product->item_name;
            $vSize = (string)($v['size'] ?? '-');
            $vColor = (string)($v['color'] ?? '-');
            $vBarcode = (string)($v['barcode'] ?? '');
            $vPrice = (float)($v['sale_price'] ?? ($product->sale_price_per_piece ?? 0));

            // Initial stock is assigned to initial warehouse only (or all if $warehouseId is null)
            $vInitial = 0;
            if ($warehouseId === null || ($initialWhId && $warehouseId == $initialWhId)) {
                $vInitial = (float)($v['orig_initial_stock'] ?? $v['stock'] ?? 0);
            }

            // Calculate Purchased
            $purchased = 0;
            foreach ($purchasesList as $pItem) {
                if ($this->matchSaleItemToVariant($pItem, $v)) {
                    $purchased += (float) $pItem->total_pieces;
                }
            }

            // Calculate Purchase Returned
            $pReturned = 0;
            foreach ($purchaseReturnsList as $prItem) {
                if ($this->matchSaleItemToVariant($prItem, $v)) {
                    $pReturned += (float) $prItem->qty;
                }
            }

            // Calculate Sold
            $sold = 0;
            foreach ($salesList as $sItem) {
                if ($this->matchSaleItemToVariant($sItem, $v)) {
                    $sold += (float) $sItem->total_pieces;
                }
            }

            // Calculate Returned
            $returnedQty = 0;
            foreach ($returnsList as $rItem) {
                $rColor = $rItem->color;
                if (empty($rColor)) {
                    $saleColors = $saleItemsMap[$rItem->sale_id] ?? [];
                    $rColor = !empty($saleColors) ? $saleColors[0] : '';
                }
                $rItemCopy = (object)[
                    'qty' => $rItem->qty,
                    'color' => $rColor
                ];
                if ($this->matchSaleItemToVariant($rItemCopy, $v)) {
                    $returnedQty += (float) $rItem->qty;
                }
            }

            // Transfers IN
            $transferInQty = 0;
            foreach ($transfersIn as $tIn) {
                if ($this->matchTransferToVariant($tIn, $v)) {
                    $transferInQty += (float) $tIn->quantity;
                }
            }

            // Transfers OUT
            $transferOutQty = 0;
            foreach ($transfersOut as $tOut) {
                if ($this->matchTransferToVariant($tOut, $v)) {
                    $transferOutQty += (float) $tOut->quantity;
                }
            }

            // Adjustments
            $adjustments = 0;
            foreach ($adjList as $adjItem) {
                if ($warehouseId !== null) {
                    if (!empty($adjItem->note) && strpos($adjItem->note, "Warehouse #{$warehouseId}") === false) {
                        continue;
                    }
                }
                if ($this->matchAdjustmentToVariant($adjItem, $v)) {
                    $adjustments += (float) $adjItem->qty;
                }
            }

            $vUncapped = $vInitial + $purchased - $sold + $returnedQty - $pReturned + $transferInQty - $transferOutQty + $adjustments;
            $vCurrent = max(0, $vUncapped);

            $results[] = [
                'variant_key'   => $vName . '|' . $vSize . '|' . $vColor,
                'name'          => $vName,
                'size'          => $vSize,
                'color'         => $vColor,
                'barcode'       => $vBarcode,
                'price'         => $vPrice,
                'initial_stock' => $vInitial,
                'current_stock' => $vCurrent,
                'uncapped_stock'=> $vUncapped,
                'display_label' => "{$vName} (Size: {$vSize}, Color: {$vColor})",
                'raw_variant'   => $v,
            ];
        }

        return $results;
    }

    /**
     * Match a sale/purchase item's color field to a variant.
     */
    public function matchSaleItemToVariant($saleItem, $variant): bool
    {
        $itemColor = $saleItem->color ?? null;
        if (empty($itemColor)) {
            return false;
        }

        $itemVariant = [];
        $b64Decoded = base64_decode($itemColor, true);
        if ($b64Decoded !== false) {
            $json = json_decode($b64Decoded, true);
            if (is_array($json)) {
                $itemVariant = $json;
            }
        }
        if (empty($itemVariant)) {
            $json = json_decode($itemColor, true);
            if (is_array($json)) {
                $itemVariant = $json;
            }
        }

        if (empty($itemVariant)) {
            return strtolower(trim($itemColor)) === strtolower(trim($variant['color'] ?? ''));
        }

        $vColor = strtolower(trim($variant['color'] ?? '-'));
        $vSize = strtolower(trim($variant['size'] ?? '-'));

        $itemVColor = strtolower(trim($itemVariant['color'] ?? ($itemVariant['color_val'] ?? '-')));
        $itemVSize = strtolower(trim($itemVariant['size'] ?? ($itemVariant['size_val'] ?? '-')));

        if ($vColor === '') $vColor = '-';
        if ($vSize === '') $vSize = '-';
        if ($itemVColor === '') $itemVColor = '-';
        if ($itemVSize === '') $itemVSize = '-';

        return $vColor === $itemVColor && $vSize === $itemVSize;
    }

    /**
     * Match a stock transfer to a variant.
     */
    public function matchTransferToVariant($transferItem, $variant): bool
    {
        $vColor = strtolower(trim($variant['color'] ?? '-'));
        $vSize  = strtolower(trim($variant['size'] ?? '-'));

        $tColor = strtolower(trim($transferItem->color ?? '-'));
        $tSize  = strtolower(trim($transferItem->size ?? '-'));

        if ($vColor === '') $vColor = '-';
        if ($vSize === '') $vSize = '-';
        if ($tColor === '') $tColor = '-';
        if ($tSize === '') $tSize = '-';

        if ($vColor === $tColor && $vSize === $tSize) {
            return true;
        }

        if (!empty($transferItem->variant_data)) {
            $vKey = $variant['name'] . '|' . ($variant['size'] ?? '-') . '|' . ($variant['color'] ?? '-');
            if (strpos($transferItem->variant_data, $vKey) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match a stock adjustment note to a variant.
     */
    public function matchAdjustmentToVariant($adjItem, $variant): bool
    {
        $note = strtolower($adjItem->note ?? '');
        if (empty($note)) {
            return false;
        }

        $vName  = strtolower(trim($variant['name'] ?? ''));
        $vSize  = strtolower(trim($variant['size'] ?? '-'));
        $vColor = strtolower(trim($variant['color'] ?? '-'));

        if (preg_match('/variant:\s*([^|\(]+)/i', $note, $matches)) {
            $noteVariantName = strtolower(trim($matches[1]));
            if (!empty($vName) && $noteVariantName !== $vName) {
                return false;
            }
            if (!empty($vName) && $noteVariantName === $vName) {
                $sizeMatch = ($vSize === '-' || empty($vSize)) ? true : (preg_match('/\b' . preg_quote($vSize, '/') . '\b/i', $note) === 1);
                $colorMatch = ($vColor === '-' || empty($vColor)) ? true : (preg_match('/\b' . preg_quote($vColor, '/') . '\b/i', $note) === 1);
                return $sizeMatch && $colorMatch;
            }
        }

        $hasSpecific = ($vSize !== '-' && !empty($vSize)) || ($vColor !== '-' && !empty($vColor));
        if ($hasSpecific) {
            $sizeMatch = ($vSize === '-' || empty($vSize)) ? true : (preg_match('/\b' . preg_quote($vSize, '/') . '\b/i', $note) === 1);
            $colorMatch = ($vColor === '-' || empty($vColor)) ? true : (preg_match('/\b' . preg_quote($vColor, '/') . '\b/i', $note) === 1);
            return $sizeMatch && $colorMatch;
        }

        return !empty($vName) && (strpos($note, $vName) !== false);
    }

    /**
     * Compute live variant stock balances for the Main Shop.
     * Shop stock is exclusively populated via stock transfers to shop (to_shop = 1)
     * minus shop sales, plus customer sale returns.
     *
     * @param Product $product
     * @return array
     */
    public function getShopVariantsWithStock(Product $product): array
    {
        $rawColor = $product->color;
        if (empty($rawColor)) {
            return [];
        }

        try {
            $parsed = is_string($rawColor) ? json_decode($rawColor, true) : $rawColor;
            if (!is_array($parsed) || count($parsed) === 0 || (!isset($parsed[0]['name']) && !isset($parsed[0]['size']) && !isset($parsed[0]['color']))) {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }

        // 1. Fetch all stock transfers dispatched to shop
        $transfersToShop = DB::table('stock_transfers')
            ->where('product_id', $product->id)
            ->where('to_shop', 1)
            ->select('quantity', 'color', 'size', 'variant_data')
            ->get();

        // 2. Fetch sales from shop (retail sales)
        $salesList = DB::table('sale_items')
            ->where('product_id', $product->id)
            ->select('total_pieces', 'color')
            ->get();

        // 3. Fetch sale returns
        $returnsList = DB::table('sale_return_items as sri')
            ->join('sale_returns as sr', 'sr.id', '=', 'sri.sale_return_id')
            ->where('sri.product_id', $product->id)
            ->select('sri.qty', 'sri.color', 'sr.sale_id')
            ->get();

        $saleIds = $returnsList->pluck('sale_id')->unique()->filter()->toArray();
        $saleItemsMap = [];
        if (!empty($saleIds)) {
            $siList = DB::table('sale_items')
                ->whereIn('sale_id', $saleIds)
                ->where('product_id', $product->id)
                ->select('sale_id', 'color')
                ->get();
            foreach ($siList as $si) {
                $saleItemsMap[$si->sale_id][] = $si->color;
            }
        }

        // 4. Fetch warehouse stock per variant for cross-comparison
        $whVariants = $this->getVariantsWithStock($product);
        $whMap = [];
        foreach ($whVariants as $wv) {
            $whMap[$wv['variant_key']] = (float)$wv['current_stock'];
        }

        $results = [];
        foreach ($parsed as $v) {
            $vName = $v['name'] ?? $product->item_name;
            $vSize = (string)($v['size'] ?? '-');
            $vColor = (string)($v['color'] ?? '-');
            $vBarcode = (string)($v['barcode'] ?? '');
            $vPrice = (float)($v['sale_price'] ?? ($product->sale_price_per_piece ?? 0));
            $vWholesale = (float)($v['wholesale_price'] ?? 0);
            $vPurch = (float)($v['purch_price'] ?? 0);
            $vAlert = (int)($v['alert'] ?? 0);
            $vKey = $vName . '|' . $vSize . '|' . $vColor;

            // Transferred to Shop
            $transferredIn = 0;
            foreach ($transfersToShop as $tIn) {
                if ($this->matchTransferToVariant($tIn, $v)) {
                    $transferredIn += (float) $tIn->quantity;
                }
            }

            // Sold in Shop
            $sold = 0;
            foreach ($salesList as $sItem) {
                if ($this->matchSaleItemToVariant($sItem, $v)) {
                    $sold += (float) $sItem->total_pieces;
                }
            }

            // Returned to Shop
            $returned = 0;
            foreach ($returnsList as $rItem) {
                $rColor = $rItem->color;
                if (empty($rColor)) {
                    $saleColors = $saleItemsMap[$rItem->sale_id] ?? [];
                    $rColor = !empty($saleColors) ? $saleColors[0] : '';
                }
                $rItemCopy = (object)[
                    'qty' => $rItem->qty,
                    'color' => $rColor
                ];
                if ($this->matchSaleItemToVariant($rItemCopy, $v)) {
                    $returned += (float) $rItem->qty;
                }
            }

            $vShopStock = max(0, $transferredIn - $sold + $returned);
            $vWarehouseStock = $whMap[$vKey] ?? 0;

            $results[] = [
                'variant_key'     => $vKey,
                'name'            => $vName,
                'size'            => $vSize,
                'color'           => $vColor,
                'barcode'         => $vBarcode,
                'price'           => $vPrice,
                'wholesale_price' => $vWholesale,
                'purch_price'     => $vPurch,
                'alert'           => $vAlert,
                'transferred_in'  => $transferredIn,
                'sold'            => $sold,
                'returned'        => $returned,
                'shop_stock'      => $vShopStock,
                'warehouse_stock' => $vWarehouseStock,
                'total_stock'     => $vShopStock + $vWarehouseStock,
                'current_stock'   => $vShopStock,
                'display_label'   => "{$vName} (Size: {$vSize}, Color: {$vColor})",
                'raw_variant'     => $v,
            ];
        }

        return $results;
    }

    /**
     * Compute total shop stock for a product.
     *
     * @param Product $product
     * @return float
     */
    public function getShopTotalStock(Product $product): float
    {
        $hasVariants = false;
        if (!empty($product->color)) {
            $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
            $hasVariants = is_array($parsed) && count($parsed) > 0 && (isset($parsed[0]['name']) || isset($parsed[0]['size']) || isset($parsed[0]['color']));
        }

        if ($hasVariants) {
            $shopVariants = $this->getShopVariantsWithStock($product);
            return (float) collect($shopVariants)->sum('shop_stock');
        }

        $transferredToShop = (float) DB::table('stock_transfers')
            ->where('product_id', $product->id)
            ->where('to_shop', 1)
            ->sum('quantity');

        $sold = (float) DB::table('sale_items')
            ->where('product_id', $product->id)
            ->sum('total_pieces');

        $returned = (float) DB::table('sale_return_items')
            ->where('product_id', $product->id)
            ->sum('qty');

        return max(0, $transferredToShop - $sold + $returned);
    }
}
