<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Services\AgentCodeService;
use App\Services\SessionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AgentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Agents List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $sessions = SessionResolver::all();
        $currentSessionLabel = SessionResolver::activeSessionLabel($request->get('session'));

        return view('admin.agents.index', compact('sessions', 'currentSessionLabel'));
    }

    public function datatable(Request $request)
    {
        $sessionLabel = SessionResolver::activeSessionLabel($request->get('session'));

        $agents = User::query()
            ->where('role', 'agent')
            ->withCount(['applications' => function ($query) use ($sessionLabel) {
                if ($sessionLabel) {
                    $query->inSession($sessionLabel);
                }
            }])
            ->withSum(['applications as commission_total' => function ($query) use ($sessionLabel) {
                if ($sessionLabel) {
                    $query->inSession($sessionLabel);
                }
            }], 'commission_amount')
            ->withSum('payouts as payouts_total', 'amount');

        if ($request->is_trashed == 'true') {
            $agents->onlyTrashed();
        }

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
            ->filterColumn('applications', function ($query, $keyword) { /* Do nothing */
            })
            ->filterColumn('commission', function ($query, $keyword) { /* Do nothing */
            })
            ->filterColumn('payouts', function ($query, $keyword) { /* Do nothing */
            })

            ->addColumn('action', function ($agent) use ($request) {
                if ($request->is_trashed == 'true') {
                    return '
                        <form action="'.route('admin.agents.restore', $agent->id).'" method="POST" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent(\'confirm-action\', { detail: { form: this, title: \'Restore agent?\', message: \'Are you sure you want to restore this agent?\' } }));" style="display:inline;">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-success">Restore</button>
                        </form>
                    ';
                }

                $toggle = $agent->is_active ? 'Suspend' : 'Activate';

                $actions = '
                <a href="'.route('admin.agents.show', $agent).'" class="btn btn-sm btn-primary">View</a>
                <a href="'.route('admin.agents.edit', $agent).'" class="btn btn-sm btn-warning">Edit</a>
                <form method="POST" action="'.route('admin.agents.toggle-status', $agent).'" style="display:inline;">
                    '.csrf_field().method_field('PATCH').'
                    <button class="btn btn-sm btn-danger">'.$toggle.'</button>
                </form>
                <form method="POST" action="'.route('admin.agents.destroy', $agent).'" style="display:inline;" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent(\'confirm-action\', { detail: { form: this, title: \'Delete agent?\', message: \'Are you sure you want to delete this agent?\' } }));">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">Delete</button>
                </form>
                ';

                return $actions;
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'mobile_number' => 'nullable|string|max:20', // New validation
            'whatsapp_no' => 'nullable|string|max:20', // New validation
            'address' => 'nullable|string|max:500', // New validation
        ]);

        $agent = User::forceCreate([
            'agent_code' => AgentCodeService::generate(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'agent',
            'is_active' => true,
            'mobile_number' => $data['mobile_number'] ?? null, // New field
            'whatsapp_no' => $data['whatsapp_no'] ?? null,   // New field
            'address' => $data['address'] ?? null,       // New field
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

    public function show(User $agent, Request $request): View
    {
        $sessions = SessionResolver::all();
        $currentSessionLabel = SessionResolver::activeSessionLabel($request->get('session'));

        $agent->loadMissing(['marketer']);

        $baseAppQuery = Application::where('agent_id', $agent->id)->inSession($currentSessionLabel);

        $totalApplications = (clone $baseAppQuery)->count();
        $completedApplications = (clone $baseAppQuery)->where('status', 'COMPLETED')->count();
        $pendingApplications = (clone $baseAppQuery)
            ->whereNotIn('status', ['COMPLETED', 'DRAFT', 'CANCELLED', 'REJECTED'])
            ->where('payment_status', '!=', 'FAILED')
            ->count();
        $incompleteApplications = (clone $baseAppQuery)
            ->where(function ($q) {
                $q->whereIn('status', ['DRAFT', 'CANCELLED', 'REJECTED'])
                    ->orWhere('payment_status', 'FAILED');
            })
            ->count();

        $totalRevenue = (clone $baseAppQuery)
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'REJECTED'])
            ->where('payment_status', '!=', 'FAILED')
            ->sum('amount');

        $commissionTotal = (clone $baseAppQuery)
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'REJECTED'])
            ->where('payment_status', '!=', 'FAILED')
            ->sum('commission_amount');

        $netRevenue = max(0, $totalRevenue - $commissionTotal);
        $avgCommission = $totalApplications > 0 ? round($commissionTotal / $totalApplications, 2) : 0;
        $completionRate = $totalApplications > 0 ? round(($completedApplications / $totalApplications) * 100, 1) : 0;

        $stats = [
            'applications' => $totalApplications,
            'completed' => $completedApplications,
            'pending' => $pendingApplications,
            'incomplete' => $incompleteApplications,
            'completion_rate' => $completionRate,
            'total_revenue' => $totalRevenue,
            'commission_total' => $commissionTotal,
            'net_revenue' => $netRevenue,
            'avg_commission' => $avgCommission,
        ];

        // Service-wise breakdown
        $serviceStats = (clone $baseAppQuery)
            ->selectRaw('service_id, COUNT(id) as count, COALESCE(SUM(amount), 0) as total_amount, COALESCE(SUM(commission_amount), 0) as total_commission')
            ->groupBy('service_id')
            ->with('service:id,name,slug')
            ->orderByDesc('count')
            ->get();

        // Applications list with quick status filter
        $statusFilter = $request->get('status', 'all');
        $applicationsListQuery = (clone $baseAppQuery)->with(['service:id,name,slug']);

        if ($statusFilter === 'completed') {
            $applicationsListQuery->where('status', 'COMPLETED');
        } elseif ($statusFilter === 'pending') {
            $applicationsListQuery->whereNotIn('status', ['COMPLETED', 'DRAFT', 'CANCELLED', 'REJECTED'])
                ->where('payment_status', '!=', 'FAILED');
        } elseif ($statusFilter === 'incomplete') {
            $applicationsListQuery->where(function ($q) {
                $q->whereIn('status', ['DRAFT', 'CANCELLED', 'REJECTED'])
                    ->orWhere('payment_status', 'FAILED');
            });
        }

        if ($search = $request->get('search')) {
            $applicationsListQuery->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('form_data', 'like', "%{$search}%")
                    ->orWhereHas('service', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $applications = $applicationsListQuery->latest()->paginate(15)->withQueryString();

        return view('admin.agents.show', compact(
            'agent',
            'stats',
            'sessions',
            'currentSessionLabel',
            'serviceStats',
            'applications',
            'statusFilter'
        ));
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
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:6|confirmed',
            'mobile_number' => 'nullable|string|max:20', // New validation
            'whatsapp_no' => 'nullable|string|max:20', // New validation
            'address' => 'nullable|string|max:500', // New validation
        ]);

        $agent->name = $data['name'];
        $agent->email = $data['email'];
        $agent->mobile_number = $data['mobile_number'] ?? null; // New field
        $agent->whatsapp_no = $data['whatsapp_no'] ?? null;   // New field
        $agent->address = $data['address'] ?? null;       // New field

        if (! empty($data['password'])) {
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
        $agent->is_active = ! $agent->is_active;
        $agent->save();

        return back()->with('success', 'Agent status updated');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Agent
    |--------------------------------------------------------------------------
    */

    public function destroy(User $agent)
    {
        $agent->delete();

        return back()->with('success', 'Agent deleted successfully');
    }

    public function restore($id)
    {
        $agent = User::withTrashed()->findOrFail($id);
        $agent->restore();

        return back()->with('success', 'Agent restored successfully');
    }
}
