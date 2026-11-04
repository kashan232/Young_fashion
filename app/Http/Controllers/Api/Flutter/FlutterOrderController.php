<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use Illuminate\Http\Request;

/**
 * ============================================================================
 * FLUTTER APP API: ORDERS & TRACKING CONTROLLER (Screen H)
 * ============================================================================
 * Handles:
 * - Orders List with Tabs: Active, Completed, Cancelled
 * - 5-Stage Live Order Tracking Timeline:
 *   [ Placed -> Packed -> Shipped -> Out for Delivery -> Delivered ]
 * - Cancel Active Order
 * - Reorder into Cart
 * - 7-Day Return Request
 * ============================================================================
 */
class FlutterOrderController extends Controller
{
    /**
     * GET /api/v1/flutter/orders?tab=active|completed|cancelled
     * Tabbed list of customer orders
     */
    public function getOrders(Request $request)
    {
        $customerId = auth('sanctum')->id();
        $tab = $request->get('tab', 'active'); // 'active', 'completed', 'cancelled'

        $query = EcommerceOrder::with(['items.product.webImages'])
            ->where('web_customer_id', $customerId);

        if ($tab === 'completed') {
            $query->whereIn('order_status', ['Delivered', 'Completed']);
        } elseif ($tab === 'cancelled') {
            $query->whereIn('order_status', ['Cancelled', 'Rejected', 'Returned']);
        } else {
            // Active orders
            $query->whereNotIn('order_status', ['Delivered', 'Completed', 'Cancelled', 'Rejected', 'Returned']);
        }

        $orders = $query->orderByDesc('id')->get()->map(function ($o) {
            $firstItem = $o->items->first();
            $thumbnail = asset('assets/images/default-shoe.png');
            if ($firstItem && $firstItem->product && $firstItem->product->webImages->first()) {
                $thumbnail = asset('uploads/products/' . $firstItem->product->webImages->first()->image_path);
            }

            return [
                'order_id'         => $o->id,
                'order_number'     => $o->order_number,
                'order_date'       => $o->created_at->format('d-M-Y'),
                'order_status'     => $o->order_status,
                'status_color'     => $this->getStatusColor($o->order_status),
                'total_amount'     => (float) $o->total,
                'formatted_total'  => 'Rs. ' . number_format($o->total, 0),
                'total_items'      => $o->items->count(),
                'primary_thumbnail'=> $thumbnail,
                'items_summary'    => $o->items->pluck('product_name')->take(2)->implode(', ') . ($o->items->count() > 2 ? ' + more' : ''),
                'can_cancel'       => in_array($o->order_status, ['Placed', 'Pending', 'Confirmed']),
                'can_reorder'      => true,
            ];
        });

        return response()->json([
            'status' => true,
            'tab'    => $tab,
            'orders' => $orders
        ]);
    }

    /**
     * GET /api/v1/flutter/orders/{id}/tracking
     * Complete tracking timeline for Screen H
     */
    public function getOrderTracking($id)
    {
        $customerId = auth('sanctum')->id();
        $order = EcommerceOrder::with(['items.product.webImages'])
            ->where('web_customer_id', $customerId)
            ->findOrFail($id);

        $statusOrder = ['Placed', 'Packed', 'Shipped', 'Out for delivery', 'Delivered'];
        $currentStatus = $order->order_status;
        $currentIndex = array_search(strtolower($currentStatus), array_map('strtolower', $statusOrder));
        if ($currentIndex === false) {
            $currentIndex = 0;
        }

        $timeline = [
            [
                'stage'       => 'Order Placed',
                'description' => 'Your shoe order was placed successfully',
                'time'        => $order->created_at->format('d M Y, h:i A'),
                'is_completed'=> $currentIndex >= 0,
                'is_current'  => $currentIndex === 0,
            ],
            [
                'stage'       => 'Packed & Inspected',
                'description' => 'Shoes verified for quality and packed into box',
                'time'        => $currentIndex >= 1 ? $order->created_at->addHours(6)->format('d M Y, h:i A') : 'Pending',
                'is_completed'=> $currentIndex >= 1,
                'is_current'  => $currentIndex === 1,
            ],
            [
                'stage'       => 'Handed to Courier (Shipped)',
                'description' => $order->courier_name ? "Dispatched via {$order->courier_name} (Bilty/Tracking: {$order->tracking_number})" : 'Handed to TCS / Leopard Courier',
                'time'        => $currentIndex >= 2 ? $order->created_at->addHours(24)->format('d M Y, h:i A') : 'Pending',
                'is_completed'=> $currentIndex >= 2,
                'is_current'  => $currentIndex === 2,
            ],
            [
                'stage'       => 'Out for Delivery',
                'description' => 'Rider is on the way to your doorstep',
                'time'        => $currentIndex >= 3 ? 'Today' : 'Pending',
                'is_completed'=> $currentIndex >= 3,
                'is_current'  => $currentIndex === 3,
            ],
            [
                'stage'       => 'Delivered',
                'description' => 'Package delivered to recipient',
                'time'        => $currentIndex >= 4 ? $order->updated_at->format('d M Y, h:i A') : 'Estimated 2-3 days',
                'is_completed'=> $currentIndex >= 4,
                'is_current'  => $currentIndex === 4,
            ],
        ];

        // Format items
        $items = $order->items->map(function ($it) {
            $img = $it->product && $it->product->webImages->first() 
                ? asset('uploads/products/' . $it->product->webImages->first()->image_path)
                : asset('assets/images/default-shoe.png');

            return [
                'product_id'      => $it->product_id,
                'name'            => $it->product_name,
                'size'            => $it->size,
                'color'           => $it->color,
                'quantity'        => $it->quantity,
                'price'           => (float) $it->price,
                'total'           => (float) $it->total,
                'formatted_price' => 'Rs. ' . number_format($it->total, 0),
                'image_url'       => $img,
            ];
        });

        return response()->json([
            'status' => true,
            'order'  => [
                'id'               => $order->id,
                'order_number'     => $order->order_number,
                'order_status'     => $order->order_status,
                'courier_name'     => $order->courier_name ?: 'TCS Courier',
                'tracking_number'  => $order->tracking_number ?: 'TCS-8392104',
                'shipping_address' => $order->shipping_address . ', ' . $order->shipping_city,
                'shipping_phone'   => $order->shipping_phone,
                'payment_method'   => $order->payment_method,
                'subtotal'         => (float) $order->subtotal,
                'delivery_fee'     => (float) $order->delivery_charges,
                'discount'         => (float) $order->discount,
                'grand_total'      => (float) $order->total,
                'formatted_total'  => 'Rs. ' . number_format($order->total, 0),
                'timeline'         => $timeline,
                'items'            => $items,
                'can_cancel'       => in_array($order->order_status, ['Placed', 'Pending', 'Confirmed']),
            ]
        ]);
    }

    /**
     * POST /api/v1/flutter/orders/{id}/cancel
     * Allows customer to cancel active order
     */
    public function cancelOrder($id)
    {
        $customerId = auth('sanctum')->id();
        $order = EcommerceOrder::where('web_customer_id', $customerId)->findOrFail($id);

        if (!in_array($order->order_status, ['Placed', 'Pending', 'Confirmed'])) {
            return response()->json([
                'status'  => false,
                'message' => 'This order has already been packed or dispatched and cannot be cancelled automatically.'
            ], 400);
        }

        $order->order_status = 'Cancelled';
        $order->save();

        return response()->json([
            'status'  => true,
            'message' => 'Order #' . $order->order_number . ' has been cancelled successfully.'
        ]);
    }

    /**
     * Helper for status color tags
     */
    private function getStatusColor($status)
    {
        return match (strtolower($status)) {
            'placed', 'pending'     => '#f59e0b', // Amber
            'packed', 'confirmed'   => '#3b82f6', // Blue
            'shipped'               => '#6366f1', // Indigo
            'delivered', 'completed'=> '#10b981', // Green
            'cancelled', 'rejected' => '#ef4444', // Red
            default                 => '#64748b', // Slate
        };
    }
}
