<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{


  

   // 1. Show the list of Operators
    public function index()
    {
        $teamMembers = User::whereIn('role', ['team', 'TEAM'])->orderBy('id', 'desc')->get();

        $assignedCounts = \App\Models\Application::whereIn('assigned_to', $teamMembers->pluck('id'))
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED', 'REJECTED']) // Optional: Only count active/pending tasks
            ->selectRaw('assigned_to, count(*) as count')
            ->groupBy('assigned_to')
            ->pluck('count', 'assigned_to');

        return view('admin.team.index', compact('teamMembers', 'assignedCounts'));
    }

    // --- 2. Show Operator Profile & Financials ---
    public function show($id)
    {
        $operator = User::whereIn('role', ['team', 'TEAM'])->findOrFail($id);

        $totalAssigned = \App\Models\Application::where('assigned_to', $operator->id)->count();
        $totalCompleted = \App\Models\Application::where('assigned_to', $operator->id)->where('status', 'COMPLETED')->count();
        $totalPending = $totalAssigned - $totalCompleted;

        $applications = \App\Models\Application::with(['service'])
                            ->where('assigned_to', $operator->id)
                            ->orderBy('updated_at', 'desc')
                            ->paginate(15);

        $services = \App\Models\Service::orderBy('name')->get();

        $currentRates = \Illuminate\Support\Facades\DB::table('operator_service_rates')
                            ->where('operator_id', $operator->id)
                            ->pluck('price', 'service_id');

        $payouts = \Illuminate\Support\Facades\DB::table('operator_payouts')
                        ->where('operator_id', $operator->id)
                        ->orderBy('paid_at', 'desc')
                        ->get();

        // Calculate Total Earned
        $totalEarned = \App\Models\Application::where('assigned_to', $operator->id)
            ->where('status', 'COMPLETED')
            ->join('operator_service_rates', function($join) use ($operator) {
                $join->on('applications.service_id', '=', 'operator_service_rates.service_id')
                     ->where('operator_service_rates.operator_id', '=', $operator->id);
            })
            ->sum('operator_service_rates.price');

        $totalPaid = $payouts->sum('amount');
        $balanceDue = $totalEarned - $totalPaid;

        $monthlyEarnings = \App\Models\Application::where('assigned_to', $operator->id)
            ->where('status', 'COMPLETED')
            ->join('operator_service_rates', function($join) use ($operator) {
                $join->on('applications.service_id', '=', 'operator_service_rates.service_id')
                     ->where('operator_service_rates.operator_id', '=', $operator->id);
            })
            // Groups the completed applications by the Month and Year they were completed
            ->selectRaw('DATE_FORMAT(applications.updated_at, "%M %Y") as month_name, DATE_FORMAT(applications.updated_at, "%Y-%m") as month_sort, COUNT(applications.id) as total_apps, SUM(operator_service_rates.price) as monthly_total')
            ->groupBy('month_name', 'month_sort')
            ->orderBy('month_sort', 'desc')
            ->get();

        return view('admin.team.show', compact(
            'operator', 'totalAssigned', 'totalCompleted', 'totalPending',
            'applications', 'services', 'currentRates', 'payouts',
            'totalEarned', 'totalPaid', 'balanceDue', 'monthlyEarnings' // Added here
        ));
    }

    // --- Save the Custom Service Rates ---
    public function saveRates(Request $request, $id)
    {
        $operator = User::whereIn('role', ['team', 'TEAM'])->findOrFail($id);
        
        $rates = $request->input('rates', []); // Array of [service_id => price]
        
        foreach ($rates as $serviceId => $price) {
            \Illuminate\Support\Facades\DB::table('operator_service_rates')->updateOrInsert(
                ['operator_id' => $operator->id, 'service_id' => $serviceId],
                ['price' => $price ?: 0, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Operator Service Rates updated successfully!');
    }

    // --- Log a new Payout --- 
    public function addPayout(Request $request, $id)
    {
        $operator = User::whereIn('role', ['team', 'TEAM'])->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_note' => 'nullable|string|max:255'
        ]);

        \Illuminate\Support\Facades\DB::table('operator_payouts')->insert([
            'operator_id' => $operator->id,
            'amount' => $request->amount,
            'payment_note' => $request->payment_note,
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.team.show', $operator->id)
            ->with('success', 'Payment of ₹' . number_format($request->amount, 2) . ' logged successfully!');
    }

   // 3. Create Operator
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            
            'password' => $request->password, 
            
            'mobile_number' => $request->phone, 
            
            'role' => 'TEAM',
            'is_active' => true,
        ]);

        return redirect()->route('admin.team.index')->with('success', 'Operator created successfully!');
    }

    // 5. Update Operator
    public function update(Request $request, $id)
    {
        $teamMember = User::whereIn('role', ['team', 'TEAM'])->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teamMember->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $teamMember->name = $request->name;
        $teamMember->email = $request->email;
        
        $teamMember->mobile_number = $request->phone;

        if ($request->filled('password')) {
            $teamMember->password = $request->password;
        }

        $teamMember->save();

        return redirect()->route('admin.team.index')->with('success', 'Operator updated successfully!');
    }

    // 4. Delete an Operator
    public function destroy($id)
    {
        $teamMember = User::where('role', 'team')->findOrFail($id);
        $teamMember->delete();

        return back()->with('success', 'Operator removed completely.');
    }

    // Loads the new dedicated Create Page
    public function create()
    {
        return view('admin.team.create');
    }

    // Loads the new dedicated Edit Page
    public function edit($id)
    {
        $teamMember = User::where('role', 'team')->findOrFail($id);
        return view('admin.team.edit', compact('teamMember'));
    }

    // Flips the user between Active (1) and Suspended (0)
    public function toggleStatus($id)
    {
        $teamMember = User::where('role', 'team')->findOrFail($id);
        $teamMember->is_active = !$teamMember->is_active; // Flips the boolean
        $teamMember->save();

        $status = $teamMember->is_active ? 'Activated' : 'Suspended';
        return back()->with('success', "Operator account has been {$status}.");
    }

    // --- Load the Dedicated Payout Page ---
    public function createPayout($id)
    {
        $operator = User::whereIn('role', ['team', 'TEAM'])->findOrFail($id);

        $totalEarned = \App\Models\Application::where('assigned_to', $operator->id)
            ->where('status', 'COMPLETED')
            ->join('operator_service_rates', function($join) use ($operator) {
                $join->on('applications.service_id', '=', 'operator_service_rates.service_id')
                     ->where('operator_service_rates.operator_id', '=', $operator->id);
            })
            ->sum('operator_service_rates.price');

        $totalPaid = \Illuminate\Support\Facades\DB::table('operator_payouts')
                        ->where('operator_id', $operator->id)
                        ->sum('amount');
                        
        $balanceDue = $totalEarned - $totalPaid;

        // 1. Fetch all earnings, ordering from OLDEST to NEWEST
        $rawEarnings = \App\Models\Application::where('assigned_to', $operator->id)
            ->where('status', 'COMPLETED')
            ->join('operator_service_rates', function($join) use ($operator) {
                $join->on('applications.service_id', '=', 'operator_service_rates.service_id')
                     ->where('operator_service_rates.operator_id', '=', $operator->id);
            })
            ->selectRaw('DATE_FORMAT(applications.updated_at, "%M %Y") as month_name, SUM(operator_service_rates.price) as monthly_total, DATE_FORMAT(applications.updated_at, "%Y-%m") as month_sort')
            ->groupBy('month_name', 'month_sort')
            ->orderBy('month_sort', 'asc') // Oldest first!
            ->get();

        // 2. FIFO Logic: Deduct the Total Paid amount from the oldest months first
        $unpaidMonths = collect();
        $paidPool = $totalPaid;

        foreach ($rawEarnings as $month) {
            $earned = $month->monthly_total;
            
            if ($paidPool >= $earned) {
                // The admin has paid enough to fully cover this month
                $paidPool -= $earned;
                $unpaid = 0;
            } else {
                // The admin has partially paid or not paid this month at all
                $unpaid = $earned - $paidPool;
                $paidPool = 0; // Pool is empty now
            }

            if ($unpaid > 0) {
                // Only save this month if they are still owed money for it
                $month->unpaid_balance = $unpaid;
                $unpaidMonths->push($month);
            }
        }

        // 3. Sort back to Newest First for the dropdown UI
        $monthlyEarnings = $unpaidMonths->sortByDesc('month_sort')->values();

        return view('admin.team.create-payout', compact('operator', 'balanceDue', 'monthlyEarnings'));
    }
}