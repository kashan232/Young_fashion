<?php

namespace App\Http\Controllers\Api\Flutter;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * ============================================================================
 * FLUTTER APP API: APP CONFIG & ONBOARDING CONTROLLER (Screens A & J)
 * ============================================================================
 * Handles:
 * - Splash screen assets (logo, animation url, app version)
 * - 3 Onboarding slides (“Latest shoes”, “Easy checkout”, “Fast delivery”)
 * - System settings, Help & Support contact, Urdu/English strings
 * ============================================================================
 */
class FlutterAppController extends Controller
{
    /**
     * GET /api/v1/flutter/app/splash-onboarding
     * Returns splash assets and 3 onboarding slides
     */
    public function splashOnboarding()
    {
        $companyName = Setting::get('company_name', 'Young Fashion');
        $companyLogo = Setting::get('site_logo') ?: Setting::get('company_logo');
        $logoUrl = $companyLogo ? asset($companyLogo) : asset('assets/images/logo.png');

        return response()->json([
            'status' => true,
            'splash' => [
                'app_name'    => $companyName,
                'app_tagline' => 'Footwear & Fashion Lifestyle',
                'app_version' => '1.0.0',
                'logo_url'    => $logoUrl,
            ],
            'onboarding_slides' => [
                [
                    'id'          => 1,
                    'title'       => 'Latest Shoes & Trends',
                    'subtitle'    => 'Discover handcrafted loafers, casual sneakers, and formal shoes designed for comfort and elegance.',
                    'image_url'   => asset('assets/images/onboarding/slide1.png'),
                    'badge'       => 'Premium Quality',
                ],
                [
                    'id'          => 2,
                    'title'       => 'Easy & Secure Checkout',
                    'subtitle'    => 'Seamless shopping with Cash on Delivery (COD), JazzCash, EasyPaisa, and debit/credit cards.',
                    'image_url'   => asset('assets/images/onboarding/slide2.png'),
                    'badge'       => '100% Safe Payments',
                ],
                [
                    'id'          => 3,
                    'title'       => 'Fast Doorstep Delivery',
                    'subtitle'    => 'Get your favorite footwear delivered directly to your door across Pakistan with live order tracking.',
                    'image_url'   => asset('assets/images/onboarding/slide3.png'),
                    'badge'       => 'Express Courier',
                ]
            ],
            'can_skip' => true
        ]);
    }

    /**
     * GET /api/v1/flutter/app/settings
     * General store settings, support numbers, policies, and localization
     */
    public function settings()
    {
        return response()->json([
            'status' => true,
            'store_info' => [
                'name'             => Setting::get('company_name', 'Young Fashion'),
                'address'          => Setting::get('company_address', 'Shaibazar, Hyderabad, Pakistan'),
                'phone'            => Setting::get('company_phone', '+92 317 3836223'),
                'whatsapp'         => '+923173836223',
                'email'            => Setting::get('company_email', 'support@youngfashion.pk'),
                'currency'         => 'PKR',
                'currency_symbol'  => 'Rs.',
                'free_delivery_min'=> 4000.00,
                'standard_delivery'=> 200.00,
            ],
            'support' => [
                'helpline'      => '+92 317 3836223',
                'whatsapp_link' => 'https://wa.me/923173836223?text=Hi%20Young%20Fashion%2C%20I%20need%20help%20with%20an%20order',
                'faq_url'       => url('/faq'),
                'terms_url'     => url('/terms-and-conditions'),
                'privacy_url'   => url('/privacy-policy'),
            ],
            'languages' => [
                ['code' => 'en', 'name' => 'English', 'default' => true],
                ['code' => 'ur', 'name' => 'اردو (Urdu)', 'default' => false]
            ]
        ]);
    }
}
