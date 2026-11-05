<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::post('/receive-sync', [SyncController::class, 'receiveSync'])->name('api.receive_sync');

// ---------------------------------------------------------
// SHORT AUTH ROUTES (Compatible with Flutter default routes)
// ---------------------------------------------------------
Route::post('/register', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'login']);

// Legacy website routes
Route::get('/settings', [\App\Http\Controllers\Api\WebsiteSettingsApiController::class, 'index']);
Route::get('/categories', [\App\Http\Controllers\Api\CategoryApiController::class, 'index']);
Route::get('/products', [\App\Http\Controllers\Api\ProductApiController::class, 'index']);
Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductApiController::class, 'show']);
Route::post('/checkout', [\App\Http\Controllers\Api\CheckoutApiController::class, 'placeOrder']);
Route::post('/checkout/validate-coupon', [\App\Http\Controllers\Api\CheckoutApiController::class, 'validateCoupon']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => true,
            'user'   => $request->user()
        ]);
    });
    Route::get('/customer/orders', [\App\Http\Controllers\Api\CustomerDashboardController::class, 'getOrders']);
});

// ============================================================================
// 📱 FLUTTER MOBILE APP V1 API ROUTES (Modules A - J)
// ============================================================================

Route::prefix('v1/flutter')->group(function () {

    // ── Module A: Onboarding & Authentication ──
    Route::get('/app/splash-onboarding', [\App\Http\Controllers\Api\Flutter\FlutterAppController::class, 'splashOnboarding']); // Splash & 3 Onboarding slides
    Route::get('/app/settings', [\App\Http\Controllers\Api\Flutter\FlutterAppController::class, 'settings']); // Store info, helpline, Urdu/English

    Route::post('/auth/register', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'register']); // Signup (creates in users table with usertype: App_user)
    Route::post('/auth/login', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'login']); // Login (Email or Phone)
    Route::post('/auth/social-login', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'socialLogin']); // Google / Apple Sign In
    Route::post('/auth/forgot-password', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'forgotPassword']); // OTP generation
    Route::post('/auth/verify-otp', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'verifyOtp']); // 6-digit OTP verification
    Route::post('/auth/reset-password', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'resetPassword']); // Reset password

    // ── Module B & C: Home Screen ──
    Route::get('/home', [\App\Http\Controllers\Api\Flutter\FlutterHomeController::class, 'index']); // Fast unified Home payload

    // ── Module D: Search & Explore ──
    Route::get('/search/trending', [\App\Http\Controllers\Api\Flutter\FlutterProductController::class, 'trendingKeywords']); // Trending & recent keywords
    Route::get('/search/filters', [\App\Http\Controllers\Api\Flutter\FlutterProductController::class, 'filterMetadata']); // Filter Bottom Sheet options (Brands, Sizes, Colors, Price slider)
    Route::get('/products', [\App\Http\Controllers\Api\Flutter\FlutterProductController::class, 'searchAndExplore']); // Search, Multi-Filter & Sort

    // ── Module E: Product Details (Sab Se Important Screen) ──
    Route::get('/products/{id}', [\App\Http\Controllers\Api\Flutter\FlutterProductController::class, 'showProductDetails']); // Color image gallery, Sizes with live in_stock, Reviews, Recommendations

    // ── Module F: Cart & Promo Code ──
    Route::post('/cart/calculate', [\App\Http\Controllers\Api\Flutter\FlutterCartController::class, 'calculateCart']); // Live stock check, Stepper +/- total, Promo discount, Delivery fee

    // ── Module G: Checkout (Can be accessed Publicly or with Auth) ──
    Route::get('/checkout/init', [\App\Http\Controllers\Api\Flutter\FlutterCheckoutController::class, 'initCheckout']); // Saved addresses, Payment methods (COD, Card, JazzCash, Easypaisa)
    Route::post('/checkout/place-order', [\App\Http\Controllers\Api\Flutter\FlutterCheckoutController::class, 'placeOrder']); // Order creation, Stock deduction, Success screen data

    // ── Protected Routes (Require Bearer Token) ──
    Route::middleware('auth:sanctum')->group(function () {

        // Auth management
        Route::post('/auth/logout', [\App\Http\Controllers\Api\Flutter\FlutterAuthController::class, 'logout']);

        // Module H: Orders & Live Tracking
        Route::get('/orders', [\App\Http\Controllers\Api\Flutter\FlutterOrderController::class, 'getOrders']); // Tabs: active | completed | cancelled
        Route::get('/orders/{id}/tracking', [\App\Http\Controllers\Api\Flutter\FlutterOrderController::class, 'getOrderTracking']); // 5-stage tracking timeline
        Route::post('/orders/{id}/cancel', [\App\Http\Controllers\Api\Flutter\FlutterOrderController::class, 'cancelOrder']); // Cancel order

        // Module I: Wishlist
        Route::get('/wishlist', [\App\Http\Controllers\Api\Flutter\FlutterWishlistController::class, 'getWishlist']); // Saved shoes grid
        Route::post('/wishlist/toggle', [\App\Http\Controllers\Api\Flutter\FlutterWishlistController::class, 'toggleWishlist']); // Heart icon tap

        // Module J: Profile
        Route::get('/profile', [\App\Http\Controllers\Api\Flutter\FlutterProfileController::class, 'getProfile']); // Customer info & stats
        Route::post('/profile/update', [\App\Http\Controllers\Api\Flutter\FlutterProfileController::class, 'updateProfile']); // Edit profile
        Route::post('/profile/change-password', [\App\Http\Controllers\Api\Flutter\FlutterProfileController::class, 'changePassword']); // Change password
    });
});
