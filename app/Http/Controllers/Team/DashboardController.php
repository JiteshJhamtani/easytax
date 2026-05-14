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

        return view('team.dashboard', compact('applications'));
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

    // 3. Update Status 
    public function updateStatus(Request $request, $id)
    {
        $application = Application::where('id', $id)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        $request->validate([
            'status' => 'required|string'
        ]);

        $application->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Application status updated successfully! The Admin and Agent can now see this update.');
    }

    // ==========================================
    // DOCUMENT MANAGEMENT
    // ==========================================

    public function uploadDocument(Request $request, $id)
    {
        // Security: Ensure task is assigned to this operator
        $application = Application::where('id', $id)->where('assigned_to', Auth::id())->firstOrFail();

        $request->validate([
            'document'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ack_file'         => 'nullable|file|mimes:pdf|max:5120',
            'computation_file' => 'nullable|file|mimes:pdf|max:5120',
            'moa_file'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'aoa_file'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('document')) {
            $application->addMediaFromRequest('document')->toMediaCollection('admin_uploads');
        }
        if ($request->hasFile('ack_file')) {
            $application->addMediaFromRequest('ack_file')->toMediaCollection('itr_acknowledgement');
        }
        if ($request->hasFile('computation_file')) {
            $application->addMediaFromRequest('computation_file')->toMediaCollection('computation_sheet');
        }
        if ($request->hasFile('moa_file')) {
            $application->addMediaFromRequest('moa_file')->toMediaCollection('moa_document');
        }
        if ($request->hasFile('aoa_file')) {
            $application->addMediaFromRequest('aoa_file')->toMediaCollection('aoa_document');
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

        return response()->file($media->getPath());
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
            'application'  => $application,
            'sales'        => $sales,
            'otherIncome'  => $otherIncome,
            'netProfit'    => $netProfit,
            'extractedData'=> $formData
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
        return $pdf->stream('Balance_Sheet_' . $applicantName . '.pdf');
    }
}