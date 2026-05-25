<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApplicationsExport;

class ApplicationController extends Controller
{

    public function index()
    {
        $services = Service::where('active', true)->get();

        $agents = User::where('role', 'agent')->get();

        $stats = Application::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status != 'COMPLETED' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN payment_status = 'FAILED' THEN 1 ELSE 0 END) as failed
        ")->first();

        return view('admin.applications.index', compact(
            'services',
            'agents',
            'stats'
        ));
    }


    public function data(Request $request)
    {

        $query = Application::with(['service', 'agent']);

        if ($request->agent) {
            $query->where('agent_id', $request->agent);
        }

        if ($request->service) {
            $query->where('service_id', $request->service);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment) {
            $query->where('payment_status', $request->payment);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return datatables()->of($query)

            ->addColumn('checkbox', fn($a) => '
                <input type="checkbox" class="row-select" value="' . $a->id . '">
            ')

            ->addColumn('agent', fn($a) => $a->agent->name)

            ->addColumn('service', fn($a) => $a->service->name)

            ->addColumn('status', fn($a) => '<span class="badge badge-info">' . $a->status->value . '</span>')

            ->addColumn('payment', fn($a) => '<span class="badge badge-success">' . $a->payment_status->value . '</span>')

            ->addColumn('amount', fn($a) => '₹' . number_format($a->amount, 2))

            ->addColumn('date', fn($a) => $a->created_at->format('d M Y'))

            ->addColumn('actions', function ($a) {

                return '
                <a href="' . route('admin.applications.show', $a) . '"
                class="btn btn-sm btn-primary">
                View
                </a>';

            })

            ->rawColumns(['checkbox', 'status', 'payment', 'actions'])

            ->make(true);
    }


    public function export(Request $request)
    {
        return Excel::download(
            new ApplicationsExport($request),
            'applications.xlsx'
        );
    }


    public function bulk(Request $request)
    {

        $ids = $request->ids;

        if (!$ids) {
            return back()->with('error', 'No rows selected.');
        }

        Application::whereIn('id', $ids)
            ->update(['status' => 'IN_PROGRESS']);

        return back()->with('success', 'Applications updated.');
    }


    public function show(Application $application)
    {
        $application->load(['service', 'agent']);

        return view('admin.applications.show', compact('application'));
    }

    public function retry(Application $application)
    {
        $transactionId = 'TXN' . time() . rand(1000, 9999);
        $application->update([
            'payment_reference' => $transactionId,
            'payment_status'    => PaymentStatus::PENDING,
        ]);

        $phonePe = new PhonePeService();
        $response = $phonePe->createPayment(
            $transactionId,
            (int) ($application->amount * 100),
            (string) auth()->id(),
            route('payment.redirect'),
            route('payment.webhook')
        );
        PaymentLog::create([
            'application_id' => $application->id,
            'transaction_id' => $transactionId,
            'event'          => 'retry',
            'response'       => $response,
        ]);
        if (isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            return redirect()->away(
                $response['data']['instrumentResponse']['redirectInfo']['url']
            );
        }
        return back()->with('error', 'Retry failed.');
    }

    public function updateStatus(Application $application, Request $request)
    {
        $application->update(['status' => $request->status]);

        return back()->with('success', 'Application status updated.');
        
    }

}
