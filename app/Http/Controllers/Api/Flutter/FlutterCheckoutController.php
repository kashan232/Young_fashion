<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * ============================================================================
 * FLUTTER APP API: CHECKOUT CONTROLLER (Screen G)
 * ============================================================================
 * Handles:
 * Step 1: Address selection & adding new address
 * Step 2: Payment method selection (COD, Card, JazzCash, EasyPaisa)
 * Step 3: Review and Place Order
 * - Stock auto-deduction
 * - Order ID generation
 * - Order Success screen response
 * ============================================================================
 */
class FlutterCheckoutController extends Controller
{
    /**
     * GET /api/v1/flutter/checkout/init
     * Pre-loads addresses and payment gateways for checkout screen
     */
    public function initCheckout(Request $request)
    {
        $user = auth('sanctum')->user();

        // 1. Saved Addresses
        $savedAddresses = [];
        if ($user) {
            $savedAddresses[] = [
                'id'           => 1,
                'name'         => $user->name,
                'phone'        => $user->phone ?: '',
                'city'         => $user->city ?: 'Hyderabad',
                'full_address' => $user->address ?: '',
                'is_default'   => true,
            ];
        }

        // 2. Payment Options
        $paymentMethods = [
            [
                'id'          => 'COD',
                'title'       => 'Cash on Delivery (COD)',
                'subtitle'    => 'Pay with cash upon doorstep delivery',
                'icon_url'    => asset('assets/images/payments/cod.png'),
                'is_active'   => true,
                'is_default'  => true,
            ],
            [
                'id'          => 'Card',
                'title'       => 'Credit / Debit Card',
                'subtitle'    => 'Visa, MasterCard, PayPak supported',
                'icon_url'    => asset('assets/images/payments/card.png'),
                'is_active'   => true,
                'is_default'  => false,
            ],
            [
                'id'          => 'JazzCash',
                'title'       => 'JazzCash Mobile Account',
                'subtitle'    => 'Instant mobile wallet payment',
                'icon_url'    => asset('assets/images/payments/jazzcash.png'),
                'is_active'   => true,
                'is_default'  => false,
            ],
            [
                'id'          => 'Easypaisa',
                'title'       => 'Easypaisa Wallet',
                'subtitle'    => 'Pay via Easypaisa app or account',
                'icon_url'    => asset('assets/images/payments/easypaisa.png'),
                'is_active'   => true,
                'is_default'  => false,
            ],
        ];

        return response()->json([
            'status'          => true,
            'saved_addresses' => $savedAddresses,
            'payment_methods' => $paymentMethods,
            'delivery_policy' => [
                'free_threshold' => 4000.00,
                'standard_fee'   => 200.00,
                'estimated_days' => '2-4 Business Days',
            ]
        ]);
    }

    /**
     * POST /api/v1/flutter/checkout/place-order
     * Final Order Placement (Screen G Step 3)
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_name'       => 'required|string|max:255',
            'shipping_phone'      => 'required|string|max:25',
            'shipping_address'    => 'required|string|max:500',
            'shipping_city'       => 'required|string|max:100',
            'payment_method'      => 'required|string|in:COD,Card,JazzCash,Easypaisa',
            'transaction_id'      => 'nullable|string|max:100',
            'order_notes'         => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.size'        => 'nullable|string',
            'items.*.color'       => 'nullable|string',
            'coupon_code'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Please provide complete delivery and payment details',
                'errors'  => $validator->errors()
            ], 422);
        }

        $customerId = auth('sanctum')->id();

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

            // 1. Verify stocks and calculate subtotal
            foreach ($request->items as $itemData) {
                $product = Product::withSum('warehouseStocks as total_stock', 'total_pieces')
                    ->lockForUpdate()
                    ->findOrFail($itemData['product_id']);

                $stock = (int) ($product->total_stock ?? 0);
                $qty = (int) $itemData['quantity'];

                if ($stock < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => false,
                        'message' => "Insufficient stock for '{$product->item_name}'. Only {$stock} pairs left.",
                    ], 400);
                }

                $price = (float) ($product->web_sale_price ?: ($product->sale_price_per_piece ?: 0));
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $orderItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->item_name,
                    'price'        => $price,
                    'quantity'     => $qty,
                    'size'         => $itemData['size'] ?? '-',
                    'color'        => $itemData['color'] ?? '-',
                    'total'        => $lineTotal,
                ];
            }

            // 2. Calculate Delivery & Coupon
            $deliveryFee = ($subtotal >= 4000) ? 0.00 : 200.00;
            $discountAmount = 0.00;
            $couponId = null;

            if ($request->filled('coupon_code')) {
                $coupon = Coupon::where('code', $request->coupon_code)->where('is_active', true)->first();
                if ($coupon) {
                    $couponId = $coupon->id;
                    if ($coupon->type === 'fixed') {
                        $discountAmount = (float) $coupon->value;
                    } elseif ($coupon->type === 'percent') {
                        $discountAmount = ($subtotal * (float)$coupon->value) / 100;
                    }
                    $discountAmount = min($discountAmount, $subtotal);
                    $coupon->increment('uses');
                }
            }

            $grandTotal = max(0, ($subtotal - $discountAmount) + $deliveryFee);

            // 3. Generate Unique Order Number
            $orderNumber = 'YF-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // 4. Create Order Record
            $order = EcommerceOrder::create([
                'order_number'       => $orderNumber,
                'web_customer_id'    => $customerId,
                'subtotal'           => $subtotal,
                'discount'           => $discountAmount,
                'coupon_id'          => $couponId,
                'coupon_code'        => $request->coupon_code,
                'delivery_charges'   => $deliveryFee,
                'total'              => $grandTotal,
                'payment_method'     => $request->payment_method,
                'transaction_id'     => $request->transaction_id,
                'paid_amount'        => ($request->payment_method === 'COD') ? 0 : $grandTotal,
                'payment_status'     => ($request->payment_method === 'COD') ? 'Pending' : 'Paid',
                'order_status'       => 'Placed',
                'is_stock_deducted'  => 1,
                'order_notes'        => $request->order_notes,
                'shipping_name'      => $request->shipping_name,
                'shipping_phone'     => $request->shipping_phone,
                'shipping_address'   => $request->shipping_address,
                'shipping_city'      => $request->shipping_city,
            ]);

            // 5. Save Items & Deduct Stock
            foreach ($orderItems as $item) {
                $item['ecommerce_order_id'] = $order->id;
                EcommerceOrderItem::create($item);

                // Auto deduct from first warehouse stock
                $whStock = WarehouseStock::where('product_id', $item['product_id'])->first();
                if ($whStock && $whStock->total_pieces >= $item['quantity']) {
                    $whStock->decrement('total_pieces', $item['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'status'         => true,
                'message'        => 'Order placed successfully!',
                'order_success'  => [
                    'order_id'         => $order->id,
                    'order_number'     => $order->order_number,
                    'total_amount'     => $grandTotal,
                    'formatted_total'  => 'Rs. ' . number_format($grandTotal, 0),
                    'payment_method'   => $order->payment_method,
                    'order_date'       => $order->created_at->format('d-M-Y h:i A'),
                    'shipping_address' => $order->shipping_address . ', ' . $order->shipping_city,
                    'estimated_delivery'=> date('d-M-Y', strtotime('+3 days')),
                    'track_order_url'  => url('/api/v1/flutter/orders/' . $order->id . '/tracking'),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }
}
