<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
   public function index()
    {
        // Fetch all coupons, newest first
        $coupons = DB::table('coupons')->orderBy('id', 'desc')->get();
        
        // Fetch all active agents to populate the dropdown in the modal
        $agents = \App\Models\User::where('role', 'agent')->where('is_active', true)->get();
        
        return view('admin.coupons.index', compact('coupons', 'agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'bonus_amount' => 'required|numeric|min:1',
            'max_uses_per_agent' => 'required|integer|min:1',
            'global_max_uses' => 'nullable|integer|min:1',
            'target_agents' => 'nullable|array', // Tells Laravel to expect an array of IDs!
        ]);

        DB::table('coupons')->insert([
            'code' => strtoupper(trim($request->code)),
            'bonus_amount' => $request->bonus_amount,
            
            // Convert the array of selected agent IDs (e.g. [1, 5, 12]) into a JSON string
            'target_agents' => $request->target_agents ? json_encode($request->target_agents) : null,
            
            'global_max_uses' => $request->global_max_uses,
            'max_uses_per_agent' => $request->max_uses_per_agent,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Promo code created successfully!');
    }

    public function toggle($id)
    {
        $coupon = DB::table('coupons')->where('id', $id)->first();
        if ($coupon) {
            DB::table('coupons')->where('id', $id)->update(['is_active' => !$coupon->is_active]);
        }
        return back()->with('success', 'Promo code status updated!');
    }

    public function destroy($id)
    {
        DB::table('coupons')->where('id', $id)->delete();
        return back()->with('success', 'Promo code deleted permanently!');
    }
}