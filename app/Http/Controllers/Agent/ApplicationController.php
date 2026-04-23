<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Enums\PaymentStatus;
use Illuminate\Support\Str;
use App\Services\PhonePeService;
use App\Models\PaymentLog;
use Spatie\MediaLibrary\MediaCollections\Models\Media; // Required for documents

class ApplicationController extends Controller
{
    public function index()
    {
        $stats    = Application::where('agent_id', auth()->id())
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status != 'COMPLETED' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = 'FAILED' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE()) THEN 1 ELSE 0 END) as monthly
            ")
            ->first();
        $services = \App\Models\Service::where('active', true)->get();

        return view('agent.applications.index', compact('stats', 'services'));
    }


    public function data(Request $request)
    {
        $query = Application::with('service')
            ->where('agent_id', auth()->id());

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

        if ($request->filter === 'pending') {
            $query->where('status', '!=', 'COMPLETED');
        }

        if ($request->filter === 'completed') {
            $query->where('status', 'COMPLETED');
        }

        if ($request->filter === 'failed') {
            $query->where('payment_status', 'FAILED');
        }

        return datatables()->of($query)
            ->addColumn('service', fn($a) => $a->service->name)
            ->addColumn('status', fn($a) => '<span class="badge badge-info">' . $a->status->value . '</span>')
            ->addColumn('payment', fn($a) => '<span class="badge badge-success">' . $a->payment_status->value . '</span>')
            ->addColumn('amount', fn($a) => '₹' . number_format($a->amount, 2))
            ->addColumn('date', fn($a) => $a->created_at->format('d M Y'))
            ->addColumn('actions', function ($a) {
                return '
<a href="' . route('agent.applications.show', $a) . '"
class="btn btn-sm btn-primary">
View
</a>';
            })
            ->rawColumns(['status', 'payment', 'actions'])
            ->make(true);
    }


    public function show(Application $application)
    {
        abort_if($application->agent_id !== auth()->id(), 403);

        $application->load(['service','media']);

        return view('agent.applications.show', compact('application'));
    }


    public function retry(Application $application)
    {
        abort_if($application->agent_id !== auth()->id(), 403);

        if ($application->payment_status === PaymentStatus::SUCCESS) {
            return back()->with('error', 'Payment already completed.');
        }

        $transactionId = 'TXN_' . Str::uuid();

        $application->update([
            'payment_reference' => $transactionId,
            'payment_status'    => PaymentStatus::PENDING
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
            'response'       => $response
        ]);

        if (isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            return redirect()->away(
                $response['data']['instrumentResponse']['redirectInfo']['url']
            );
        }

        return back()->with('error', 'Retry failed.');
    }

    public function cancel(Application $application)
    {
        abort_if($application->agent_id !== auth()->id(), 403);

        // ONLY stop them if it is ALREADY cancelled. 
        if ($application->status === \App\Enums\ApplicationStatus::CANCELLED) {
            return back()->with('error', 'Application is already cancelled.');
        }

        // Change status and zero out the commission
        $application->update([
            'status' => \App\Enums\ApplicationStatus::CANCELLED,
            'commission_amount' => 0 
        ]);

        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Application cancelled by agent');

        $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ApplicationCancelledNotification($application));

        return back()->with('success', 'Application has been successfully cancelled.');
    }

    /*
    |--------------------------------------------------------------------------
    | Secure Document Viewing for Agent
    |--------------------------------------------------------------------------
    */

    public function viewDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);

        // STRICT SECURITY: Must be an agent AND must own the application this file belongs to!
        if (strtoupper(auth()->user()->role) !== 'AGENT' || $media->model->agent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this document.');
        }

        $path = storage_path('app/private/' . $media->id . '/' . $media->file_name);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file($path);
    }

    public function downloadDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);

        // STRICT SECURITY: Must be an agent AND must own the application this file belongs to!
        if (strtoupper(auth()->user()->role) !== 'AGENT' || $media->model->agent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this document.');
        }

        $path = storage_path('app/private/' . $media->id . '/' . $media->file_name);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path, $media->file_name);
    }
}