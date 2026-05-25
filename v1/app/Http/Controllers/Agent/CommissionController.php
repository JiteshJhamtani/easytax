<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AgentPayout;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CommissionController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Unpaid commissions
    |--------------------------------------------------------------------------
    */

    public function commissions()
    {
        return view('agent.commissions.index');
    }

    public function commissionsTable()
    {
        $query = Application::with('service')
            ->where('agent_id', Auth::id())
            ->whereNull('payout_id')
            ->where('payment_status', 'SUCCESS');

        return DataTables::eloquent($query)

            ->addColumn('service', fn($row) => $row->service->name)

            ->addColumn(
                'commission',
                fn($row) =>
                '₹' . number_format($row->commission_amount, 2)
            )

            ->addColumn(
                'date',
                fn($row) =>
                $row->submitted_at
            )

            ->make(true);
    }


    /*
    |--------------------------------------------------------------------------
    | Agent payout history
    |--------------------------------------------------------------------------
    */

    public function payouts()
    {
        return view('agent.payouts.index');
    }

    public function payoutsTable()
    {
        $query = AgentPayout::where('agent_id', Auth::id());

        return DataTables::eloquent($query)

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

            ->rawColumns(['status'])
            ->make(true);
    }

}
