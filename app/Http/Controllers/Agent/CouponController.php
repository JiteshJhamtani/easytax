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

        // 2. Search for the coupon in the MASTER database
        $connectionName = config()->has('database.connections.master_connection') ? 'master_connection' : config('database.default');
        
        try {
            $coupon = DB::connection($connectionName)->table('coupons')->where('code', $code)->first();
        } catch (\Exception $e) {
            $coupon = DB::table('coupons')->where('code', $code)->first();
        }

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

        // SCENARIO 4: Code is locked to a single legacy service (Old Method)
        if ($coupon->service_id) {
            $service = DB::table('services')->where('slug', $serviceSlug)->first();
            if ($service && $coupon->service_id != $service->id) {
                return response()->json(['valid' => false, 'message' => 'This code cannot be used for this specific service.']);
            }
        }

        //  SCENARIO 4B: Code is locked to multiple specific services (NEW METHOD)
        if (!empty($coupon->target_services)) {
            // Fetch the service the user is currently trying to buy
            $currentService = DB::table('services')->where('slug', $serviceSlug)->first();
            
            if ($currentService) {
                // Decode the JSON array of allowed service IDs (e.g., ["1", "4"])
                $allowedServices = json_decode($coupon->target_services, true);
                
                // If it's a valid array, and the current service ID is NOT in that array, reject it
                if (is_array($allowedServices) && count($allowedServices) > 0 && !in_array($currentService->id, $allowedServices)) {
                    return response()->json(['valid' => false, 'message' => 'This promo code is not valid for the selected service.']);
                }
            }
        }

        // SCENARIO 5: Global max uses reached
        if ($coupon->global_max_uses && $coupon->total_used >= $coupon->global_max_uses) {
            return response()->json(['valid' => false, 'message' => 'This promo code has reached its maximum usage limit.']);
        }

        // SCENARIO 6: Max uses per agent reached
        $usedCount = DB::table('applications')
            ->where('agent_id', Auth::id())
            ->where('coupon_id', $coupon->id)
            ->whereNotIn('status', ['DRAFT', 'CANCELLED'])
            ->count();
            
        if ($coupon->max_uses_per_agent && $usedCount >= $coupon->max_uses_per_agent) {
            return response()->json(['valid' => false, 'message' => 'You have already used this promo code the maximum allowed times.']);
        }

        // SCENARIO 7: Code is locked to a SPECIFIC Agent (Cross-Server Check)
        if (!empty($coupon->target_agents)) {
            $allowedAgents = json_decode($coupon->target_agents, true);

            // Look up the agent's ID on the Master server by email so they can use it here
            $masterAgentId = Auth::id();
            try {
                $masterAgent = DB::connection($connectionName)->table('users')->where('email', Auth::user()->email)->first();
                if ($masterAgent) { $masterAgentId = $masterAgent->id; }
            } catch (\Exception $e) {}

            if (is_array($allowedAgents) && count($allowedAgents) > 0 && !in_array($masterAgentId, $allowedAgents) && !in_array(Auth::id(), $allowedAgents)) {
                return response()->json(['valid' => false, 'message' => 'This promo code is not assigned to your account.']);
            }
        } elseif (!empty($coupon->agent_id)) {
            $masterAgentId = Auth::id();
            try {
                $masterAgent = DB::connection($connectionName)->table('users')->where('email', Auth::user()->email)->first();
                if ($masterAgent) { $masterAgentId = $masterAgent->id; }
            } catch (\Exception $e) {}

            if ($coupon->agent_id != $masterAgentId && $coupon->agent_id != Auth::id()) {
                return response()->json(['valid' => false, 'message' => 'This promo code is not assigned to your account.']);
            }
        }

        
        return response()->json([
            'valid' => true,
            'code'  => $coupon->code,
            'bonus' => $coupon->bonus_amount
        ]);
    }
}