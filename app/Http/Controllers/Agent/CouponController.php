<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function validateCoupon(Request $request)
    {
        // 1. Clean the incoming data
        $code = strtoupper(trim($request->input('code')));
        $serviceSlug = $request->input('service_slug');

        // 2. Search for the coupon in the database
        $b2bDatabase = config('database.connections.master_connection.database', 'easytax_db');
        $coupon = DB::table($b2bDatabase . '.coupons')->where('code', $code)->first();

        // SCENARIO 1: Code doesn't exist
        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Invalid promo code.']);
        }

        // SCENARIO 2: Code was turned off by Admin
        if (!$coupon->is_active) {
            return response()->json(['valid' => false, 'message' => 'This promo code is currently disabled.']);
        }

        // SCENARIO 3: Code has expired
        if ($coupon->expires_at && now()->greaterThan($coupon->expires_at)) {
            return response()->json(['valid' => false, 'message' => 'This promo code has expired.']);
        }

        // SCENARIO 4: Code is locked to a different service
        if ($coupon->service_id) {
            $service = DB::table('services')->where('slug', $serviceSlug)->first();
            if ($service && $coupon->service_id != $service->id) {
                return response()->json(['valid' => false, 'message' => 'This code cannot be used for this specific service.']);
            }
        }

        // 🚀 NEW SCENARIO 5: Code is locked to a SPECIFIC Agent
        if ($coupon->agent_id && $coupon->agent_id != Auth::id()) {
            return response()->json(['valid' => false, 'message' => 'This promo code is not assigned to your account.']);
        }

        
        return response()->json([
            'valid' => true,
            'code'  => $coupon->code,
            'bonus' => $coupon->bonus_amount
        ]);
    }
}