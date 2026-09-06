<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// If you have a Service model, you can import it here: use App\Models\Service;

class CouponController extends Controller
{
    public function index()
    {
        // Fetch all coupons, newest first
        $coupons = DB::table('coupons')->orderBy('id', 'desc')->get();

        // Fetch all active agents to populate the dropdown
        $agents = User::where('role', 'agent')->where('is_active', true)->get();

        // NEW: Fetch all services to populate the services dropdown
        // Assuming your table is named 'services'. Adjust if you use a Model like \App\Models\Service::all()
        $services = Service::all();

        // NEW: Added $services to compact()
        return view('admin.coupons.index', compact('coupons', 'agents', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'bonus_amount' => 'required|numeric|min:1',
            'max_uses_per_agent' => 'required|integer|min:1',
            'global_max_uses' => 'nullable|integer|min:1',
            'target_agents' => 'nullable|array',
            'target_services' => 'nullable|array',
        ]);

        DB::table('coupons')->insert([
            'code' => strtoupper(trim($request->code)),
            'bonus_amount' => $request->bonus_amount,

            'target_agents' => $request->target_agents ? json_encode($request->target_agents) : null,

            // NEW: Convert the array of selected service IDs into a JSON string
            'target_services' => $request->target_services ? json_encode($request->target_services) : null,

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
            DB::table('coupons')->where('id', $id)->update(['is_active' => ! $coupon->is_active]);
        }

        return back()->with('success', 'Promo code status updated!');
    }

    public function destroy($id)
    {
        DB::table('coupons')->where('id', $id)->delete();

        return back()->with('success', 'Promo code deleted permanently!');
    }
}
