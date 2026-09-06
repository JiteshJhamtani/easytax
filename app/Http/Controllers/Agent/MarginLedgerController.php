<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentMarginLog;
use App\Models\AgentMarginPayout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MarginLedgerController extends Controller
{
    /**
     * Display the margin earnings ledger, stats, and payout history for the parent agent.
     */
    public function index(): View
    {
        $parent = auth()->user();

        $logsQuery = AgentMarginLog::where('parent_agent_id', $parent->id);

        $accruedAmount = (clone $logsQuery)->where('status', 'ACCRUED')->sum('margin_amount');
        $settledAmount = (clone $logsQuery)->where('status', 'PAID')->sum('margin_amount');
        $totalEarned = $accruedAmount + $settledAmount;
        $totalTransactions = (clone $logsQuery)->count();
        $payoutsCount = AgentMarginPayout::where('parent_agent_id', $parent->id)->count();

        $recentLogs = (clone $logsQuery)->with(['subAgent', 'application.service'])->latest()->limit(5)->get();

        $stats = [
            'accrued_amount' => (float) $accruedAmount,
            'settled_amount' => (float) $settledAmount,
            'total_earned' => (float) $totalEarned,
            'total_transactions' => $totalTransactions,
            'payouts_count' => $payoutsCount,
        ];

        return view('agent.sub_agents.margins', compact('stats', 'recentLogs', 'parent'));
    }

    /**
     * DataTable data source for margin logs.
     */
    public function data(Request $request): JsonResponse
    {
        $parent = auth()->user();

        $query = AgentMarginLog::with(['subAgent', 'application.service'])
            ->where('parent_agent_id', $parent->id);

        return DataTables::of($query)
            ->addColumn('sub_agent', fn ($row) => $row->subAgent ? e($row->subAgent->name).' ('.e($row->subAgent->agent_code).')' : 'N/A')
            ->addColumn('service', fn ($row) => $row->application?->service?->name ?? 'Service')
            ->addColumn('app_ref', fn ($row) => '<a href="'.route('agent.applications.show', $row->application_id).'" class="font-weight-bold">#'.$row->application_id.'</a>')
            ->addColumn('sub_paid', fn ($row) => '₹'.number_format((float) $row->sub_agent_paid, 2))
            ->addColumn('company_share', fn ($row) => '₹'.number_format((float) $row->company_retained, 2))
            ->addColumn('margin_amount', fn ($row) => '<span class="text-success font-weight-bold">+₹'.number_format((float) $row->margin_amount, 2).'</span>')
            ->addColumn('status', function ($row) {
                return match (strtoupper($row->status)) {
                    'ACCRUED' => '<span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i>Accrued (Pending Payout)</span>',
                    'PAID' => '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Paid to Bank</span>',
                    'CONFIRMED' => '<span class="badge badge-info px-2 py-1"><i class="fas fa-check mr-1"></i>Accrued</span>',
                    'CANCELLED' => '<span class="badge badge-danger px-2 py-1">Cancelled</span>',
                    default => '<span class="badge badge-secondary px-2 py-1">'.e($row->status).'</span>',
                };
            })
            ->addColumn('payout_info', function ($row) {
                if ($row->status === 'PAID' && $row->payout_reference) {
                    return '<code class="text-dark font-weight-bold">UTR: '.e($row->payout_reference).'</code>';
                }

                return '<span class="text-muted text-xs"><i class="fas fa-hourglass-start mr-1"></i>Awaiting Next Payout</span>';
            })
            ->addColumn('date', fn ($row) => $row->created_at ? $row->created_at->format('d M Y, h:i A') : '-')
            ->rawColumns(['app_ref', 'margin_amount', 'status', 'payout_info'])
            ->make(true);
    }

    /**
     * DataTable data source for historical payouts received from admin.
     */
    public function payoutsData(Request $request): JsonResponse
    {
        $parent = auth()->user();

        $query = AgentMarginPayout::withCount('marginLogs')
            ->where('parent_agent_id', $parent->id);

        return DataTables::of($query)
            ->addColumn('voucher', fn ($row) => '<span class="font-weight-bold text-primary">'.e($row->payout_number).'</span>')
            ->addColumn('amount', fn ($row) => '<span class="font-weight-bold text-success">₹'.number_format((float) $row->amount, 2).'</span>')
            ->addColumn('mode', function ($row) {
                return match ($row->payment_method) {
                    'bank_transfer' => '<span class="badge badge-info">Bank Transfer</span>',
                    'upi' => '<span class="badge badge-primary">UPI</span>',
                    default => '<span class="badge badge-secondary">'.e(ucfirst($row->payment_method)).'</span>',
                };
            })
            ->addColumn('reference', fn ($row) => '<code class="text-dark font-weight-bold">'.e($row->transaction_reference).'</code>')
            ->addColumn('date', fn ($row) => $row->payment_date ? $row->payment_date->format('d M Y') : '-')
            ->addColumn('items', fn ($row) => '<span class="badge badge-light border">'.$row->margin_logs_count.' apps</span>')
            ->rawColumns(['voucher', 'amount', 'mode', 'reference', 'items'])
            ->make(true);
    }

    /**
     * Update parent agent's payout receiving bank / UPI details.
     */
    public function updateBankDetails(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:100',
            'bank_upi_id' => 'nullable|string|max:100',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Your payout bank & UPI details have been updated successfully.');
    }
}
