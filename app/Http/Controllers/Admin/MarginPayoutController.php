<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentMarginPayout;
use App\Models\User;
use App\Services\AgentMarginPayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MarginPayoutController extends Controller
{
    public function __construct(
        protected AgentMarginPayoutService $payoutService
    ) {}

    /**
     * Display the Margin Payouts dashboard with KPI cards and agency tables.
     */
    public function index(): View
    {
        $kpis = $this->payoutService->getKpis();
        $pendingAgencies = $this->payoutService->getAgenciesWithAccruedMargins();

        return view('admin.margin_payouts.index', compact('kpis', 'pendingAgencies'));
    }

    /**
     * DataTables endpoint for historical margin payout disbursements.
     */
    public function table(Request $request): JsonResponse
    {
        $query = AgentMarginPayout::with(['parentAgent', 'admin'])
            ->select('agent_margin_payouts.*');

        return DataTables::eloquent($query)
            ->addColumn('voucher', fn ($row) => '<a href="'.route('admin.margin-payouts.show', $row->id).'" class="font-weight-bold text-primary">'.e($row->payout_number).'</a>')
            ->addColumn('agent', fn ($row) => $row->parentAgent ? e($row->parentAgent->name).' ('.e($row->parentAgent->agent_code).')' : 'N/A')
            ->addColumn('amount', fn ($row) => '<span class="font-weight-bold text-success">₹'.number_format((float) $row->amount, 2).'</span>')
            ->addColumn('payment_mode', function ($row) {
                return match ($row->payment_method) {
                    'bank_transfer' => '<span class="badge badge-info">Bank Transfer</span>',
                    'upi' => '<span class="badge badge-primary">UPI</span>',
                    'cheque' => '<span class="badge badge-secondary">Cheque</span>',
                    'cash' => '<span class="badge badge-dark">Cash</span>',
                    default => '<span class="badge badge-secondary">'.e(ucfirst($row->payment_method)).'</span>',
                };
            })
            ->addColumn('reference', fn ($row) => '<code class="text-dark font-weight-bold">'.e($row->transaction_reference).'</code>')
            ->addColumn('date', fn ($row) => $row->payment_date ? $row->payment_date->format('d M Y') : '-')
            ->addColumn('processed_by', fn ($row) => $row->admin->name ?? 'Admin')
            ->addColumn('actions', function ($row) {
                return '<a href="'.route('admin.margin-payouts.show', $row->id).'" class="btn btn-xs btn-outline-primary font-weight-bold px-2 py-1"><i class="fas fa-eye mr-1"></i> Details</a>';
            })
            ->rawColumns(['voucher', 'amount', 'payment_mode', 'reference', 'actions'])
            ->make(true);
    }

    /**
     * Fetch accrued logs and bank details for an agency to populate the payout modal.
     */
    public function accruedDetails(User $agent): JsonResponse
    {
        if ($agent->isSubAgent()) {
            return response()->json(['error' => 'Sub-agents do not have agency margin payouts.'], 422);
        }

        $logs = $this->payoutService->getAccruedLogs($agent);

        return response()->json([
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'agent_code' => $agent->agent_code,
                'mobile_number' => $agent->mobile_number,
                'email' => $agent->email,
                'bank_name' => $agent->bank_name,
                'bank_account_number' => $agent->bank_account_number,
                'bank_ifsc' => $agent->bank_ifsc,
                'bank_account_holder' => $agent->bank_account_holder,
                'bank_upi_id' => $agent->bank_upi_id,
            ],
            'total_amount' => round($logs->sum('margin_amount'), 2),
            'items_count' => $logs->count(),
            'logs' => $logs->map(fn ($log) => [
                'id' => $log->id,
                'application_id' => $log->application_id,
                'sub_agent_name' => $log->subAgent?->name ?? 'Team Member',
                'sub_agent_code' => $log->subAgent?->agent_code ?? '-',
                'service_name' => $log->application?->service?->name ?? 'Service',
                'sub_agent_paid' => (float) $log->sub_agent_paid,
                'company_retained' => (float) $log->company_retained,
                'margin_amount' => (float) $log->margin_amount,
                'date' => $log->created_at ? $log->created_at->format('d M Y') : '-',
            ]),
        ]);
    }

    /**
     * Settle accrued margins for the parent agent.
     */
    public function settle(Request $request, User $agent): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:bank_transfer,upi,cheque,cash,other',
            'transaction_reference' => 'required|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'log_ids' => 'nullable|array',
            'log_ids.*' => 'integer|exists:agent_margin_logs,id',
        ]);

        $payout = $this->payoutService->settle(auth()->user(), $agent, $validated);

        return response()->json([
            'success' => true,
            'message' => "Margin payout voucher #{$payout->payout_number} settled successfully!",
            'payout' => $payout,
        ]);
    }

    /**
     * Show payout voucher detail and settled items breakdown.
     */
    public function show(AgentMarginPayout $payout): View
    {
        $payout->load([
            'parentAgent',
            'admin',
            'marginLogs.subAgent',
            'marginLogs.application.service',
        ]);

        return view('admin.margin_payouts.show', compact('payout'));
    }
}
