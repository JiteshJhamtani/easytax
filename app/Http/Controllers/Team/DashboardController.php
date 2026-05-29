<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // 1. The Main Dashboard List
    public function index()
    {
        // Fetch ONLY applications assigned to the logged-in team member
        $applications = Application::with('service')
            ->where('assigned_to', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // NEW: Calculate Financial Summary for the Operator
        $totalEarned = \App\Models\Application::where('assigned_to', Auth::id())
            ->where('status', 'COMPLETED')
            ->join('operator_service_rates', function ($join) {
                $join->on('applications.service_id', '=', 'operator_service_rates.service_id')
                    ->where('operator_service_rates.operator_id', '=', Auth::id());
            })
            ->sum('operator_service_rates.price');

        $totalPaid = \Illuminate\Support\Facades\DB::table('operator_payouts')
            ->where('operator_id', Auth::id())
            ->sum('amount');

        $balanceDue = $totalEarned - $totalPaid;

        return view('team.dashboard', compact('applications', 'totalEarned', 'totalPaid', 'balanceDue'));
    }

    // 2. The Detailed View Page (Privacy Enforced!)
    public function show($id)
    {
        // Security Check: Ensure they can only view apps assigned to them
        $application = Application::with('service')
            ->where('id', $id)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        return view('team.applications.show', compact('application'));
    }

    // 3. Update Status (Now handles pending_reason AND sends emails!)
    public function updateStatus(Request $request, $id)
    {
        $application = Application::with('service')->where('id', $id)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        // Force the status to uppercase BEFORE validation runs so it doesn't get rejected!
        if ($request->has('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        }

        $request->validate([
            'status' => 'required|string|in:IN_PROGRESS,E_FILING,OTP_VERIFICATION,COMPLETED',
            'pending_reason' => 'nullable|string|max:1000',
        ]);

        $status = $request->status;

        // 1. Save the new status and note
        \Illuminate\Support\Facades\DB::transaction(function () use ($application, $status, $request) {
            $application->update([
                'status' => $status,
                'pending_reason' => $request->pending_reason,
            ]);

            if ($status === 'COMPLETED') {
                $application->update(['completed_at' => now()]);
            }

            // 2. Log Activity
            activity('application')
                ->performedOn($application)
                ->causedBy(auth()->user())
                ->log('Operator updated status to '.$status.($request->pending_reason ? ' - '.$request->pending_reason : ''));
        });

        if ($status === 'COMPLETED') {
            $emailKey = $application->service->applicant_email_field ?? null;

            if (! empty($application->form_data)) {
                $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;

                // SMART FALLBACK: If emailKey is missing in DB, try common email fields
                $clientEmail = (! empty($emailKey) && isset($formData[$emailKey]))
                    ? $formData[$emailKey]
                    : ($formData['email'] ?? $formData['email_id'] ?? $formData['applicant_email'] ?? null);

                $clientName = $formData['applicant_name'] ?? $formData['name'] ?? $formData['full_name'] ?? $formData['company_name'] ?? $formData['firm_name'] ?? 'Valued Client';
                $trackingUrl = \Illuminate\Support\Facades\URL::signedRoute('tracking.show', ['application' => $application->id]);

                if ($clientEmail && filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
                    try {
                        \Illuminate\Support\Facades\Mail::send('emails.application_completed', [
                            'application' => $application,
                            'clientName' => $clientName,
                            'trackingUrl' => $trackingUrl,
                        ], function ($message) use ($clientEmail, $application) {
                            $serviceName = $application->service->name ?? 'Service';
                            $message->to($clientEmail)
                                ->subject("Completed: Your {$serviceName} Application");
                        });
                        \Log::info("Completion email sent to {$clientEmail} for App #{$application->id}");
                    } catch (\Exception $e) {
                        \Log::error("Email failed for App #{$application->id}: ".$e->getMessage());
                    }
                }
            }
        }

        return back()->with('success', 'Application status updated successfully.');
    }
    // ==========================================
    // DOCUMENT MANAGEMENT
    // ==========================================

    public function uploadDocument(Request $request, $id)
    {
        // Security: Ensure task is assigned to this operator
        $application = Application::where('id', $id)->where('assigned_to', Auth::id())->firstOrFail();

        $request->validate([
            'document' => 'nullable|file|mimetypes:application/pdf,application/octet-stream,image/jpeg,image/png|extensions:pdf,png,jpg,jpeg|max:5120',
            'ack_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream|extensions:pdf|max:5120',
            'computation_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream|extensions:pdf|max:5120',
            'moa_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|extensions:pdf,doc,docx|max:5120',
            'aoa_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|extensions:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('document')) {
            $application->addMediaFromRequest('document')->toMediaCollection('admin_uploads', 'private');
        }
        if ($request->hasFile('ack_file')) {
            $application->addMediaFromRequest('ack_file')->toMediaCollection('itr_acknowledgement', 'private');
        }
        if ($request->hasFile('computation_file')) {
            $application->addMediaFromRequest('computation_file')->toMediaCollection('computation_sheet', 'private');
        }
        if ($request->hasFile('moa_file')) {
            $application->addMediaFromRequest('moa_file')->toMediaCollection('moa_document', 'private');
        }
        if ($request->hasFile('aoa_file')) {
            $application->addMediaFromRequest('aoa_file')->toMediaCollection('aoa_document', 'private');
        }

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function deleteDocument($mediaId)
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
        $application = Application::findOrFail($media->model_id);

        // Security: Only the assigned operator can delete docs
        if ($application->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $media->delete();

        return back()->with('success', 'Document removed.');
    }

    public function viewDocument($mediaId)
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
        $application = Application::findOrFail($media->model_id);

        if ($application->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $headers = [];
        if (\Illuminate\Support\Str::endsWith(strtolower($media->file_name), '.pdf')) {
            $headers['Content-Type'] = 'application/pdf';
        }

        return response()->file($media->getPath(), $headers)->setContentDisposition('inline', $media->file_name);
    }

    public function downloadDocument($mediaId)
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
        $application = Application::findOrFail($media->model_id);

        if ($application->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return response()->download($media->getPath(), $media->file_name);
    }

    // ==========================================
    // BALANCE SHEET GENERATION
    // ==========================================

    public function balanceSheet($id)
    {
        // Security Check
        $application = Application::with('service')->where('id', $id)->where('assigned_to', Auth::id())->firstOrFail();

        $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : ($application->form_data ?? []);

        // Extract basic data for the interactive form
        $sales = floatval($formData['sales'] ?? $formData['turnover'] ?? 0);
        $otherIncome = floatval($formData['other_income'] ?? 0);
        $netProfit = floatval($formData['target_net_profit'] ?? $formData['net_profit'] ?? 0);

        return view('team.applications.balance_sheet', [
            'application' => $application,
            'sales' => $sales,
            'otherIncome' => $otherIncome,
            'netProfit' => $netProfit,
            'extractedData' => $formData,
        ]);
    }

    public function generateBalanceSheet(Request $request, $id)
    {
        $application = Application::where('id', $id)->where('assigned_to', Auth::id())->firstOrFail();

        $data = $request->all();

        // 1. Calculate P&L Totals
        $grossProfit = ($data['sales'] + $data['closing_stock']) - ($data['opening_stock'] + $data['purchases'] + $data['direct_expenses']);
        $tradingTotal = $data['sales'] + $data['closing_stock'];

        $expenses = $data['salaries'] + $data['electricity'] + $data['shop_rent'] + $data['telephone_internet'] + $data['printing_stationery'] + $data['repairs_maintenance'] + $data['interest_on_loan'] + $data['other_expenses'];
        $netProfit = ($grossProfit + $data['interest_income'] + $data['other_income']) - $expenses;
        $pnlTotal = $expenses + $netProfit;

        // 2. Calculate Balance Sheet Totals
        $closingCapital = $data['opening_capital'] + $netProfit - $data['drawings'];
        $capitalTotal = $data['drawings'] + $closingCapital;

        $bsTotal = $closingCapital + $data['bank_loan'] + $data['other_loans'] + $data['sundry_creditors'] + $data['other_current_liabilities'];

        // Retrieve Applicant Name/PAN safely from JSON
        $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : ($application->form_data ?? []);
        $applicantName = $formData['applicant_name'] ?? $formData['name'] ?? 'Client';
        $panNumber = $formData['pan_number'] ?? $formData['pan'] ?? 'N/A';

        // 3. Generate PDF
        $pdf = \PDF::loadView('team.applications.pdf.balance_sheet', compact(
            'application', 'data', 'grossProfit', 'tradingTotal',
            'netProfit', 'pnlTotal', 'closingCapital', 'capitalTotal',
            'bsTotal', 'applicantName', 'panNumber'
        ));

        // Return inline view or force download
        return $pdf->stream('Balance_Sheet_'.$applicantName.'.pdf');
    }
}
