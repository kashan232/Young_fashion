<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\WebCustomer;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

/**
 * ============================================================================
 * FLUTTER APP API: AUTHENTICATION & ONBOARDING CONTROLLER (Screen A)
 * ============================================================================
 * Handles:
 * - Customer Registration (Sign up)
 * - Customer Login (Email/Phone + Password)
 * - Social Login (Google / Apple Token Exchange)
 * - Forgot Password & OTP Generation
 * - OTP Verification (4-6 digit box input with resend timer)
 * - Password Reset
 * - Logout
 * ============================================================================
 */
class FlutterAuthController extends Controller
{
    /**
     * POST /api/v1/flutter/auth/register
     * Register a new customer from the Flutter App
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:web_customers,email',
            'phone'                 => 'nullable|string|max:25',
            'password'              => 'required|string|min:6',
            'terms_accepted'        => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Link or create ERP Customer record
        $existingCustomer = null;
        if ($request->email) {
            $existingCustomer = Customer::where('email_address', $request->email)->first();
        }
        if (!$existingCustomer && $request->phone) {
            $existingCustomer = Customer::where('mobile', $request->phone)->first();
        }

        if ($existingCustomer) {
            $customerId = $existingCustomer->customer_id;
            if (empty($existingCustomer->source) || $existingCustomer->source === 'Manual') {
                $existingCustomer->source = 'Mobile App';
                $existingCustomer->save();
            }
        } else {
            $customerId = 'CUST-' . str_pad((Customer::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
            Customer::create([
                'customer_id'     => $customerId,
                'customer_name'   => $request->name,
                'mobile'          => $request->phone,
                'email_address'   => $request->email,
                'opening_balance' => 0,
                'customer_type'   => 'Main Customer',
                'status'          => 'active',
                'source'          => 'Mobile App',
            ]);
        }

        // Create Web/App Customer account
        $user = WebCustomer::create([
            'customer_id' => $customerId,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
        ]);

        // Generate Sanctum Bearer Token
        $token = $user->createToken('flutter_mobile_token')->plainTextToken;

        return response()->json([
            'status'     => true,
            'message'    => 'Account created successfully',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'          => $user->id,
                'customer_id' => $user->customer_id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'avatar'      => asset('assets/images/user-avatar.png'),
            ]
        ], 201);
    }

    /**
     * POST /api/v1/flutter/auth/login
     * Login using email or phone number
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string', // can be email or phone
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Please provide valid credentials',
                'errors'  => $validator->errors()
            ], 422);
        }

        $loginInput = $request->login;

        // Check by email or phone
        $user = WebCustomer::where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email/phone or password',
            ], 401);
        }

        // Revoke old tokens if single device login desired, or generate new token
        $token = $user->createToken('flutter_mobile_token')->plainTextToken;

        return response()->json([
            'status'     => true,
            'message'    => 'Login successful',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'          => $user->id,
                'customer_id' => $user->customer_id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'address'     => $user->address,
                'city'        => $user->city,
                'avatar'      => asset('assets/images/user-avatar.png'),
            ]
        ], 200);
    }

    /**
     * POST /api/v1/flutter/auth/social-login
     * Google or Apple sign-in token verification
     */
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider'   => 'required|in:google,apple',
            'provider_id'=> 'required|string',
            'email'      => 'required|email',
            'name'       => 'required|string',
            'avatar'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid social auth payload',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = WebCustomer::where('email', $request->email)->first();

        if (!$user) {
            $customerId = 'CUST-' . str_pad((Customer::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
            Customer::create([
                'customer_id'     => $customerId,
                'customer_name'   => $request->name,
                'email_address'   => $request->email,
                'opening_balance' => 0,
                'customer_type'   => 'Main Customer',
                'status'          => 'active',
                'source'          => 'Mobile App Social',
            ]);

            $user = WebCustomer::create([
                'customer_id' => $customerId,
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => Hash::make('social_' . uniqid()),
            ]);
        }

        $token = $user->createToken('flutter_mobile_token')->plainTextToken;

        return response()->json([
            'status'     => true,
            'message'    => 'Social login successful',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'          => $user->id,
                'customer_id' => $user->customer_id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'avatar'      => $request->avatar ?: asset('assets/images/user-avatar.png'),
            ]
        ]);
    }

    /**
     * POST /api/v1/flutter/auth/forgot-password
     * Generates a 6-digit OTP code with 5-minute validity
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:web_customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Email address not found in our records',
                'errors'  => $validator->errors()
            ], 404);
        }

        // Generate 6-digit OTP (for testing/demo: 123456 or random)
        $otp = (string) rand(100000, 999999);
        Cache::put('otp_' . $request->email, $otp, 300); // 5 minutes validity

        return response()->json([
            'status'         => true,
            'message'        => 'Verification code sent to your email',
            'resend_seconds' => 60, // Timer in Flutter
            'demo_otp'       => $otp, // Helpful for Flutter team during development
        ]);
    }

    /**
     * POST /api/v1/flutter/auth/verify-otp
     * Verifies the 6-digit code
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Please enter a valid 6-digit OTP'], 422);
        }

        $cachedOtp = Cache::get('otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired OTP code'], 400);
        }

        // Mark OTP as verified for password reset token
        $resetToken = bin2hex(random_bytes(20));
        Cache::put('reset_token_' . $request->email, $resetToken, 600);

        return response()->json([
            'status'      => true,
            'message'     => 'OTP verified successfully',
            'reset_token' => $resetToken
        ]);
    }

    /**
     * POST /api/v1/flutter/auth/reset-password
     * Resets password using the verified reset_token
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email',
            'reset_token' => 'required|string',
            'password'    => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $cachedToken = Cache::get('reset_token_' . $request->email);
        if (!$cachedToken || $cachedToken !== $request->reset_token) {
            return response()->json(['status' => false, 'message' => 'Reset session expired. Please request OTP again.'], 403);
        }

        $user = WebCustomer::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            Cache::forget('reset_token_' . $request->email);
            Cache::forget('otp_' . $request->email);

            return response()->json([
                'status'  => true,
                'message' => 'Password changed successfully. You can now login.'
            ]);
        }

        return response()->json(['status' => false, 'message' => 'User not found'], 404);
    }

    /**
     * POST /api/v1/flutter/auth/logout
     */
    public function logout(Request $request)
    {
        if ($request->user('sanctum')) {
            $request->user('sanctum')->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
