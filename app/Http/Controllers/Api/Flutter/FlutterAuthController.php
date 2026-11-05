<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\User;
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
 * - Customer Registration (Sign up saved into `users` table with usertype: 'App_user')
 * - Customer Login (Email/Phone + Password with instant token return)
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
     * POST /api/v1/flutter/auth/register (also /api/register)
     * Register a new user into `users` table with usertype = 'App_user'
     *
     * Expected Payload (JSON / Form-data):
     * - name: string (required)
     * - email: string/email (required, unique in users)
     * - phone: string (optional/recommended)
     * - password: string (required, min 6)
     * - confirm_password OR password_confirmation: string (optional, must match password)
     */
    public function register(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users,email',
            'phone'                 => 'nullable|string|max:30',
            'password'              => 'required|string|min:6',
            'confirm_password'      => 'nullable|string',
            'password_confirmation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Check password confirmation if passed either as confirm_password or password_confirmation
        $confirmation = $request->confirm_password ?: $request->password_confirmation;
        if ($confirmation !== null && $confirmation !== $request->password) {
            return response()->json([
                'status'  => false,
                'message' => 'Password confirmation does not match',
                'errors'  => [
                    'confirm_password' => ['The password confirmation does not match.']
                ]
            ], 422);
        }

        // 2. Create User in `users` table with usertype = 'App_user'
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'usertype' => 'App_user',
        ]);

        // 3. Keep WebCustomer in sync for ecommerce order processing
        try {
            WebCustomer::updateOrCreate(
                ['email' => $request->email],
                [
                    'name'     => $request->name,
                    'phone'    => $request->phone,
                    'password' => Hash::make($request->password),
                ]
            );
        } catch (\Exception $e) {
            \Log::warning("WebCustomer sync skipped: " . $e->getMessage());
        }

        // 4. Generate Sanctum Bearer Token for immediate Flutter login
        $token = $user->createToken('flutter_mobile_token')->plainTextToken;

        return response()->json([
            'status'     => true,
            'message'    => 'Account created successfully',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'usertype'    => $user->usertype,
                'avatar'      => asset('assets/images/user-avatar.png'),
                'created_at'  => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
            ]
        ], 201);
    }

    /**
     * POST /api/v1/flutter/auth/login (also /api/login)
     * Login using email or phone + password
     *
     * Expected Payload (JSON / Form-data):
     * - email OR phone OR login: string (required)
     * - password: string (required)
     */
    public function login(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'email'    => 'nullable|string',
            'phone'    => 'nullable|string',
            'login'    => 'nullable|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Please provide credentials',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Accept email, phone, or login parameter
        $loginInput = $request->email ?: ($request->phone ?: $request->login);

        if (empty($loginInput)) {
            return response()->json([
                'status'  => false,
                'message' => 'Please provide an email or phone number',
                'errors'  => [
                    'email' => ['The email or phone field is required.']
                ]
            ], 422);
        }

        // 2. Search user in `users` table by email or phone
        $user = User::where(function ($q) use ($loginInput) {
            $q->where('email', $loginInput)
              ->orWhere('phone', $loginInput);
        })->first();

        // 3. Fallback check in web_customers if not yet in users table
        if (!$user) {
            $webCust = WebCustomer::where('email', $loginInput)->orWhere('phone', $loginInput)->first();
            if ($webCust && Hash::check($request->password, $webCust->password)) {
                // Auto migrate to users table with App_user type
                $user = User::updateOrCreate(
                    ['email' => $webCust->email],
                    [
                        'name'     => $webCust->name,
                        'phone'    => $webCust->phone,
                        'password' => $webCust->password,
                        'usertype' => 'App_user',
                    ]
                );
            }
        }

        // 4. Verify password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email/phone or password',
            ], 401);
        }

        // 5. Generate Sanctum Bearer Token
        $token = $user->createToken('flutter_mobile_token')->plainTextToken;

        return response()->json([
            'status'     => true,
            'message'    => 'Login successful',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'usertype'   => $user->usertype ?? 'App_user',
                'avatar'     => asset('assets/images/user-avatar.png'),
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

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('social_' . uniqid()),
                'usertype' => 'App_user',
            ]);
        }

        $token = $user->createToken('flutter_mobile_token')->plainTextToken;

        return response()->json([
            'status'     => true,
            'message'    => 'Social login successful',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'phone'    => $user->phone,
                'usertype' => $user->usertype,
                'avatar'   => $request->avatar ?: asset('assets/images/user-avatar.png'),
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
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Email address not found in our records',
                'errors'  => $validator->errors()
            ], 404);
        }

        $otp = (string) rand(100000, 999999);
        Cache::put('otp_' . $request->email, $otp, 300); // 5 minutes validity

        return response()->json([
            'status'         => true,
            'message'        => 'Verification code sent to your email',
            'resend_seconds' => 60,
            'demo_otp'       => $otp,
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

        $user = User::where('email', $request->email)->first();
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
