<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Services\AgentCodeService;
use App\Services\SessionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class SubAgentController extends Controller
{
    /**
     * Display a listing of all sub-agents under the parent agent.
     */
    public function index(Request $request)
    {
        $parent = auth()->user();
        $currentSessionLabel = SessionResolver::activeSessionLabel($request->get('session'));

        $subAgents = User::where('parent_id', $parent->id)->get();
        $totalMembers = $subAgents->count();
        $activeMembers = $subAgents->where('is_active', true)->count();

        $teamAppsQuery = Application::where('agent_id', $parent->id)
            ->whereNotNull('sub_agent_id')
            ->inSession($currentSessionLabel);

        $teamApplicationsCount = (clone $teamAppsQuery)->count();
        $totalMarginEarned = (clone $teamAppsQuery)->where('payment_status', 'PAID')->sum('parent_margin');

        $kpis = [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'team_applications' => $teamApplicationsCount,
            'total_margin_earned' => (float) $totalMarginEarned,
        ];

        return view('agent.sub_agents.index', compact('kpis', 'currentSessionLabel'));
    }

    /**
     * DataTable data source for sub-agents.
     */
    public function data(Request $request)
    {
        $parent = auth()->user();
        $currentSessionLabel = SessionResolver::activeSessionLabel($request->get('session'));

        $query = User::query()
            ->where('parent_id', $parent->id)
            ->withCount(['subAgentApplications' => function ($q) use ($currentSessionLabel) {
                if ($currentSessionLabel) {
                    $q->inSession($currentSessionLabel);
                }
            }])
            ->withSum(['subAgentApplications as margin_total' => function ($q) use ($currentSessionLabel) {
                $q->where('payment_status', 'PAID');
                if ($currentSessionLabel) {
                    $q->inSession($currentSessionLabel);
                }
            }], 'parent_margin');

        return DataTables::of($query)
            ->addColumn('checkbox', fn ($row) => '<input type="checkbox" class="subagent-select" value="'.$row->id.'">')
            ->addColumn('applications', fn ($row) => (int) $row->sub_agent_applications_count)
            ->addColumn('margin_earned', fn ($row) => '₹'.number_format((float) ($row->margin_total ?? 0), 2))
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success px-2 py-1">Active</span>'
                    : '<span class="badge badge-danger px-2 py-1">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                $toggleText = $row->is_active ? 'Suspend' : 'Activate';
                $toggleClass = $row->is_active ? 'btn-outline-danger' : 'btn-outline-success';

                return '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.route('agent.sub-agents.edit', $row->id).'" class="btn btn-sm btn-outline-primary" title="Edit Member">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="'.route('agent.sub-agents.toggle-status', $row->id).'" class="d-inline">
                            '.csrf_field().method_field('PATCH').'
                            <button type="submit" class="btn btn-sm '.$toggleClass.'" title="'.$toggleText.'">
                                <i class="fas '.($row->is_active ? 'fa-user-slash' : 'fa-user-check').'"></i>
                            </button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openResetPasswordModal('.$row->id.', \''.e($row->name).'\')" title="Reset Password">
                            <i class="fas fa-key"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['checkbox', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show single create form.
     */
    public function create()
    {
        return view('agent.sub_agents.create');
    }

    /**
     * Store a newly created sub-agent.
     */
    public function store(Request $request)
    {
        $parent = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'mobile_number' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $subAgentCode = AgentCodeService::generateSubAgentCode($parent);

        User::forceCreate([
            'agent_code' => $subAgentCode,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'AGENT',
            'parent_id' => $parent->id,
            'is_active' => true,
            'mobile_number' => $data['mobile_number'] ?? null,
            'whatsapp_no' => $data['whatsapp_no'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return redirect()->route('agent.sub-agents.index')
            ->with('success', "Team member {$data['name']} added successfully with Code: {$subAgentCode}");
    }

    /**
     * Show bulk onboarding view (interactive table & CSV upload).
     */
    public function bulkCreate()
    {
        return view('agent.sub_agents.bulk_create');
    }

    /**
     * Store multiple sub-agents submitted via interactive multi-row form.
     */
    public function bulkStore(Request $request)
    {
        $parent = auth()->user();

        $request->validate([
            'members' => 'required|array|min:1',
            'members.*.name' => 'required|string|max:255',
            'members.*.email' => 'required|email|distinct|unique:users,email',
            'members.*.password' => 'required|string|min:6',
            'members.*.mobile_number' => 'nullable|string|max:20',
            'members.*.whatsapp_no' => 'nullable|string|max:20',
        ]);

        $createdCount = 0;

        DB::transaction(function () use ($request, $parent, &$createdCount) {
            foreach ($request->members as $memberData) {
                $code = AgentCodeService::generateSubAgentCode($parent);

                User::forceCreate([
                    'agent_code' => $code,
                    'name' => $memberData['name'],
                    'email' => $memberData['email'],
                    'password' => Hash::make($memberData['password']),
                    'role' => 'AGENT',
                    'parent_id' => $parent->id,
                    'is_active' => true,
                    'mobile_number' => $memberData['mobile_number'] ?? null,
                    'whatsapp_no' => $memberData['whatsapp_no'] ?? null,
                ]);

                $createdCount++;
            }
        });

        return redirect()->route('agent.sub-agents.index')
            ->with('success', "Successfully onboarded {$createdCount} team members!");
    }

    /**
     * Import sub-agents from an uploaded CSV file.
     */
    public function importCsv(Request $request)
    {
        $parent = auth()->user();

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()->with('error', 'Unable to read the uploaded CSV file.');
        }

        // Read header
        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return back()->with('error', 'The CSV file is empty.');
        }

        // Normalize header columns
        $normalizedHeader = array_map(fn ($col) => strtolower(trim($col)), $header);

        $nameIdx = array_search('name', $normalizedHeader);
        $emailIdx = array_search('email', $normalizedHeader);
        $passwordIdx = array_search('password', $normalizedHeader);
        $mobileIdx = array_search('mobile_number', $normalizedHeader);
        if ($mobileIdx === false) {
            $mobileIdx = array_search('mobile', $normalizedHeader);
        }
        $whatsappIdx = array_search('whatsapp_no', $normalizedHeader);
        if ($whatsappIdx === false) {
            $whatsappIdx = array_search('whatsapp', $normalizedHeader);
        }

        if ($nameIdx === false || $emailIdx === false || $passwordIdx === false) {
            fclose($handle);

            return back()->with('error', 'CSV header must include at least: name, email, password');
        }

        $createdCount = 0;
        $skipped = [];
        $rowNum = 1;

        DB::transaction(function () use (
            $handle, $nameIdx, $emailIdx, $passwordIdx, $mobileIdx, $whatsappIdx,
            $parent, &$createdCount, &$skipped, &$rowNum
        ) {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                $name = trim($row[$nameIdx] ?? '');
                $email = trim($row[$emailIdx] ?? '');
                $password = trim($row[$passwordIdx] ?? '');
                $mobile = $mobileIdx !== false ? trim($row[$mobileIdx] ?? '') : null;
                $whatsapp = $whatsappIdx !== false ? trim($row[$whatsappIdx] ?? '') : null;

                if (empty($name) || empty($email) || empty($password)) {
                    $skipped[] = "Row #{$rowNum}: Missing required fields.";

                    continue;
                }

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped[] = "Row #{$rowNum} ({$email}): Invalid email format.";

                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $skipped[] = "Row #{$rowNum} ({$email}): Email already registered in system.";

                    continue;
                }

                $code = AgentCodeService::generateSubAgentCode($parent);

                User::forceCreate([
                    'agent_code' => $code,
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'AGENT',
                    'parent_id' => $parent->id,
                    'is_active' => true,
                    'mobile_number' => $mobile ?: null,
                    'whatsapp_no' => $whatsapp ?: null,
                ]);

                $createdCount++;
            }
        });

        fclose($handle);

        $msg = "Import completed: {$createdCount} team members successfully created.";
        if (count($skipped) > 0) {
            $msg .= ' ('.count($skipped).' rows skipped: '.implode('; ', array_slice($skipped, 0, 3)).'...)';
        }

        return redirect()->route('agent.sub-agents.index')->with('success', $msg);
    }

    /**
     * Download a sample CSV template for bulk onboarding.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sub_agents_template.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'password', 'mobile_number', 'whatsapp_no']);
            fputcsv($handle, ['Rahul Sharma', 'rahul.team@example.com', 'Pass@12345', '9876543210', '9876543210']);
            fputcsv($handle, ['Priya Patel', 'priya.team@example.com', 'Pass@12345', '9812345678', '9812345678']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show edit form for sub-agent.
     */
    public function edit(User $subAgent)
    {
        $this->authorizeSubAgent($subAgent);

        return view('agent.sub_agents.edit', compact('subAgent'));
    }

    /**
     * Update sub-agent details.
     */
    public function update(Request $request, User $subAgent)
    {
        $this->authorizeSubAgent($subAgent);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$subAgent->id,
            'mobile_number' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $subAgent->update($data);

        return redirect()->route('agent.sub-agents.index')
            ->with('success', "Team member {$subAgent->name} updated successfully.");
    }

    /**
     * Toggle active/suspended status.
     */
    public function toggleStatus(User $subAgent)
    {
        $this->authorizeSubAgent($subAgent);

        $subAgent->is_active = ! $subAgent->is_active;
        $subAgent->save();

        $statusLabel = $subAgent->is_active ? 'activated' : 'suspended';

        return back()->with('success', "Team member {$subAgent->name} has been {$statusLabel}.");
    }

    /**
     * Reset password for an individual sub-agent.
     */
    public function resetPassword(Request $request, User $subAgent)
    {
        $this->authorizeSubAgent($subAgent);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $subAgent->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Password for {$subAgent->name} has been reset successfully.");
    }

    /**
     * Perform bulk action on selected sub-agents.
     */
    public function bulkAction(Request $request)
    {
        $parent = auth()->user();

        $data = $request->validate([
            'action' => 'required|in:activate,deactivate,reset_password',
            'sub_agent_ids' => 'required|array|min:1',
            'sub_agent_ids.*' => 'exists:users,id',
            'bulk_password' => 'nullable|required_if:action,reset_password|string|min:6',
        ]);

        $validIds = User::whereIn('id', $data['sub_agent_ids'])
            ->where('parent_id', $parent->id)
            ->pluck('id');

        if ($validIds->isEmpty()) {
            return back()->with('error', 'No valid team members selected.');
        }

        $count = $validIds->count();

        if ($data['action'] === 'activate') {
            User::whereIn('id', $validIds)->update(['is_active' => true]);
            $msg = "{$count} team member(s) activated successfully.";
        } elseif ($data['action'] === 'deactivate') {
            User::whereIn('id', $validIds)->update(['is_active' => false]);
            $msg = "{$count} team member(s) suspended successfully.";
        } elseif ($data['action'] === 'reset_password') {
            User::whereIn('id', $validIds)->update([
                'password' => Hash::make($data['bulk_password']),
            ]);
            $msg = "Password updated for {$count} team member(s).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Enforce that the target user belongs to the authenticated parent agent.
     */
    private function authorizeSubAgent(User $subAgent): void
    {
        if ($subAgent->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized. This user does not belong to your team.');
        }
    }
}
