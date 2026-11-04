<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\WebCustomer;
use App\Models\EcommerceOrder;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * ============================================================================
 * FLUTTER APP API: PROFILE & SETTINGS CONTROLLER (Screen J)
 * ============================================================================
 * Handles:
 * - Customer profile, avatar, order count stats
 * - Edit Profile (name, email, phone, city, address)
 * - Change Password
 * - Addresses list & Save address
 * ============================================================================
 */
class FlutterProfileController extends Controller
{
    /**
     * GET /api/v1/flutter/profile
     * Complete customer profile with activity stats
     */
    public function getProfile(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $totalOrders = EcommerceOrder::where('web_customer_id', $user->id)->count();
        $activeOrders = EcommerceOrder::where('web_customer_id', $user->id)
            ->whereNotIn('order_status', ['Delivered', 'Completed', 'Cancelled', 'Rejected'])
            ->count();
        $wishlistCount = Wishlist::where('web_customer_id', $user->id)->count();

        return response()->json([
            'status' => true,
            'user'   => [
                'id'             => $user->id,
                'customer_id'    => $user->customer_id,
                'name'           => $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone ?: '',
                'address'        => $user->address ?: '',
                'city'           => $user->city ?: 'Hyderabad',
                'avatar_url'     => asset('assets/images/user-avatar.png'),
                'member_since'   => $user->created_at ? $user->created_at->format('M Y') : '2026',
            ],
            'stats'  => [
                'total_orders'   => $totalOrders,
                'active_orders'  => $activeOrders,
                'wishlist_count' => $wishlistCount,
            ]
        ]);
    }

    /**
     * POST /api/v1/flutter/profile/update
     * Edit profile details
     */
    public function updateProfile(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:25',
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        if ($request->filled('phone')) $user->phone = $request->phone;
        if ($request->filled('address')) $user->address = $request->address;
        if ($request->filled('city')) $user->city = $request->city;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'user'    => $user
        ]);
    }

    /**
     * POST /api/v1/flutter/profile/change-password
     */
    public function changePassword(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|different:current_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
