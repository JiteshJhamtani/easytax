<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use App\Models\AgentPayout;
use App\Services\AgentCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AgentController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Agents List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('admin.agents.index');
    }

    public function datatable()
    {
        $agents = User::query()
            ->where('role', 'agent')
            ->withCount(['applications'])
            ->withSum('applications as commission_total', 'commission_amount')
            ->withSum('payouts as payouts_total', 'amount');

        return DataTables::of($agents)
            
            // 1. Map the calculated database columns to the Javascript names
            ->addColumn('applications', function ($agent) {
                return $agent->applications_count;
            })
            ->addColumn('commission', function ($agent) {
                return $agent->commission_total;
            })
            ->addColumn('payouts', function ($agent) {
                return $agent->payouts_total;
            })

            // 2. STOP Laravel from crashing by disabling SQL text searches on math columns
            ->filterColumn('applications', function($query, $keyword) { /* Do nothing */ })
            ->filterColumn('commission', function($query, $keyword) { /* Do nothing */ })
            ->filterColumn('payouts', function($query, $keyword) { /* Do nothing */ })

            ->addColumn('action', function ($agent) {
                $toggle = $agent->is_active ? 'Suspend' : 'Activate';

                return '
                <a href="' . route('admin.agents.show', $agent) . '" class="btn btn-sm btn-primary">View</a>
                <a href="' . route('admin.agents.edit', $agent) . '" class="btn btn-sm btn-warning">Edit</a>
                <form method="POST" action="' . route('admin.agents.toggle-status', $agent) . '" style="display:inline;">
                    ' . csrf_field() . method_field('PATCH') . '
                    <button class="btn btn-sm btn-danger">' . $toggle . '</button>
                </form>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Agent
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.agents.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $agent = User::create([
            'agent_code' => AgentCodeService::generate(),
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'agent',
            'is_active'  => true
        ]);

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Agent created successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Agent Profile / Analytics
    |--------------------------------------------------------------------------
    */

    public function show(User $agent)
    {
        $applications = Application::where('agent_id', $agent->id);

        $stats = [
            'applications'      => $applications->count(),
            'submitted'         => $applications->clone()->where('status', 'SUBMITTED')->count(),
            'commission_total'  => $applications->clone()->sum('commission_amount'),
            'commission_unpaid' => $applications->clone()->whereNull('payout_id')->sum('commission_amount'),
            'payouts_total'     => AgentPayout::where('agent_id', $agent->id)->sum('amount'),
        ];

        return view('admin.agents.show', compact('agent', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Agent
    |--------------------------------------------------------------------------
    */

    public function edit(User $agent)
    {
        return view('admin.agents.edit', compact('agent'));
    }

    public function update(Request $request, User $agent)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'nullable|min:6|confirmed'
        ]);

        $agent->name  = $data['name'];
        $agent->email = $data['email'];

        if (!empty($data['password'])) {
            $agent->password = Hash::make($data['password']);
        }

        $agent->save();

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Agent updated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Suspend / Activate Agent
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(User $agent)
    {
        $agent->is_active = !$agent->is_active;
        $agent->save();

        return back()->with('success', 'Agent status updated');
    }
}

