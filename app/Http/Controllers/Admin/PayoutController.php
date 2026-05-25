<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayoutService;
use App\Models\AgentPayout;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class PayoutController extends Controller
{
    public function index()
    {
        $agents = User::where('role', 'agent')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.payouts.index', compact('agents'));
    }

    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    public function table()
    {
        $query = AgentPayout::with('agent')
            ->select('agent_payouts.*');

        return DataTables::eloquent($query)

            ->addColumn('agent', fn($row) => $row->agent->name ?? '-')

            ->addColumn(
                'amount',
                fn($row) =>
                '₹' . number_format($row->amount, 2)
            )

            ->addColumn(
                'period',
                fn($row) =>
                $row->period_start . ' → ' . $row->period_end
            )

            ->addColumn('status', function ($row) {

                return $row->paid_at
                    ? '<span class="badge badge-success">Paid</span>'
                    : '<span class="badge badge-warning">Pending</span>';

            })

            ->addColumn('actions', function ($row) {

                return view(
                    'admin.payouts.partials.actions',
                    ['payout' => $row]
                )->render();

            })

            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | Preview payouts
    |--------------------------------------------------------------------------
    */

    public function preview(Request $request, PayoutService $service)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'agent_id'   => 'nullable|exists:users,id'
        ]);

        return response()->json(
            $service->preview(
                $request->start_date,
                $request->end_date,
                $request->agent_id
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate payouts
    |--------------------------------------------------------------------------
    */

    public function generate(Request $request, PayoutService $service)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'agent_id'   => 'nullable|exists:users,id'
        ]);

        $payouts = $service->generate(
            $request->start_date,
            $request->end_date,
            $request->agent_id
        );

        return response()->json([
            'success' => true,
            'count'   => count($payouts)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mark paid
    |--------------------------------------------------------------------------
    */

    public function markPaid(Request $request, AgentPayout $payout, PayoutService $service)
    {
        $service->markPaid($payout, $request->notes);

        return response()->json([
            'success' => true
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show payout details
    |--------------------------------------------------------------------------
    */

    public function show(AgentPayout $payout)
    {
        $payout->load([
            'agent',
            'applications.service'
        ]);

        return view(
            'admin.payouts.show',
            compact('payout')
        );
    }
}
