<?php

namespace App\Http\Controllers\Agent;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use App\Notifications\ApplicationCancelledNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'other');
        $pageTitle = match ($type) {
            'gst-return-filing' => 'My GST Return Filings',
            'itr-filing' => 'My ITR Filings',
            'gst-registration' => 'My GST Registrations',
            default => 'My Other Applications',
        };

        // --- NEW DYNAMIC KPI LOGIC ---
        $query = Application::where('agent_id', auth()->id());
        $specialSlugs = ['itr-filing', 'gst-registration', 'gst-return-filing'];

        if ($type === 'other') {
            $query->whereHas('service', function ($q) use ($specialSlugs) {
                $q->whereNotIn('slug', $specialSlugs);
            });
        } elseif (in_array($type, $specialSlugs)) {
            $query->whereHas('service', function ($q) use ($type) {
                $q->where('slug', $type);
            });
        }

        $stats = $query->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status != 'COMPLETED' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = 'FAILED' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE()) THEN 1 ELSE 0 END) as monthly
            ")->first();
        // -----------------------------

        $services = Service::where('active', true)->get();

        return view('agent.applications.index', compact('stats', 'services', 'type', 'pageTitle'));
    }

    public function data(Request $request)
    {
        $query = Application::with([
            'service',
            'media' => function ($q) {
                $q->whereIn('collection_name', ['itr_acknowledgement', 'computation_sheet', 'balance_sheet']);
            },
        ])->where('agent_id', auth()->id());

        // --- FILTERING LOGIC ---
        $type = $request->query('type', 'other');
        $specialSlugs = ['itr-filing', 'gst-registration', 'gst-return-filing'];

        if ($type === 'other') {
            $query->whereHas('service', function ($q) use ($specialSlugs) {
                $q->whereNotIn('slug', $specialSlugs);
            });
        } elseif (in_array($type, $specialSlugs)) {
            $query->whereHas('service', function ($q) use ($type) {
                $q->where('slug', $type);
            });
        }
        // ---------------------------

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

        if ($request->is_trashed == 'true') {
            $query->onlyTrashed();
        }

        return datatables()->of($query)
            ->filterColumn('service.name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('service', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%{$keyword}%");
                    })->orWhere('form_data', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('service', fn ($a) => $a->service->name)
            ->addColumn('status', fn ($a) => '<span class="badge badge-info">'.$a->status->value.'</span>')
            ->addColumn('payment', fn ($a) => '<span class="badge badge-success">'.$a->payment_status->value.'</span>')
            ->addColumn('amount', fn ($a) => '₹'.number_format($a->amount, 2))
            ->addColumn('date', fn ($a) => $a->created_at->format('d M Y'))

            // 1. ACK NUMBER COLUMN
            ->addColumn('ack_no', function ($a) {
                if ($a->service->slug !== 'itr-filing') {
                    return '-';
                }

                $ackMedia = $a->getFirstMedia('itr_acknowledgement');
                if ($ackMedia) {
                    $downloadUrl = route('agent.documents.download', $ackMedia->id);
                    $ackNumber = $ackMedia->getCustomProperty('ack_number');

                    $html = '';
                    if ($ackNumber) {
                        $html .= '<span class="d-block font-weight-bold text-dark mb-1">'.$ackNumber.'</span>';
                    }
                    $html .= '<a href="'.$downloadUrl.'" class="text-primary font-weight-bold"><i class="fas fa-download mr-1"></i> Download</a>';

                    return $html;
                }

                return '<span class="text-muted text-xs font-italic">Pending</span>';
            })

            // 2. COMPUTATION COLUMN
            ->addColumn('computation', function ($a) {
                if ($a->service->slug !== 'itr-filing') {
                    return '-';
                }

                $compMedia = $a->getFirstMedia('computation_sheet');
                if ($compMedia) {
                    $downloadUrl = route('agent.documents.download', $compMedia->id);

                    return '<a href="'.$downloadUrl.'" class="text-primary font-weight-bold"><i class="fas fa-download mr-1"></i> Download</a>';
                }

                return '<span class="text-muted text-xs font-italic">Pending</span>';
            })

            // 3. SMART BALANCE SHEET BUTTON
            ->addColumn('balance_sheet', function ($a) {
                if ($a->service->slug !== 'itr-filing') {
                    return '-';
                }

                $bsMedia = $a->getFirstMedia('balance_sheet');

                // If it has been generated, let Agent view, download, or regenerate
                if ($bsMedia) {
                    $viewUrl = route('agent.documents.view', $bsMedia->id);
                    $downloadUrl = route('agent.documents.download', $bsMedia->id);
                    $regenUrl = route('agent.applications.balance-sheet', $a->id);

                    return '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.$viewUrl.'" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1" title="View"><i class="fas fa-eye"></i></a>
                        <a href="'.$downloadUrl.'" class="btn btn-sm btn-outline-success px-2 py-1" title="Download"><i class="fas fa-download"></i></a>
                        <a href="'.$regenUrl.'" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Regenerate"><i class="fas fa-sync-alt"></i></a>
                    </div>';
                }

                // If not generated yet, show the Generate button
                $url = route('agent.applications.balance-sheet', $a->id);

                return '<a href="'.$url.'" class="btn btn-sm btn-outline-success font-weight-bold" style="white-space: nowrap;"><i class="fas fa-file-excel mr-1"></i> Generate</a>';
            })
            ->addColumn('actions', function ($a) use ($request) {
                if ($request->is_trashed == 'true') {
                    return '
                        <form action="'.route('agent.applications.restore', $a->id).'" method="POST" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent(\'confirm-action\', { detail: { form: this, title: \'Restore Application?\', message: \'Are you sure you want to restore this application?\' } }));" style="display:inline;">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-success">Restore</button>
                        </form>
                    ';
                }

                $html = '<a href="'.route('agent.applications.show', $a).'" class="btn btn-sm btn-primary">View</a>';
                $html .= '
                    <form action="'.route('agent.applications.destroy', $a->id).'" method="POST" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent(\'confirm-action\', { detail: { form: this, title: \'Move to trash?\', message: \'Are you sure you want to delete this application?\' } }));" style="display:inline; margin-left: 4px;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.25rem 0.5rem;"><i class="fas fa-trash"></i></button>
                    </form>
                ';

                return $html;
            })
            ->rawColumns(['status', 'payment', 'ack_no', 'computation', 'balance_sheet', 'actions'])
            ->make(true);
    }

    public function show(Application $application)
    {
        abort_if($application->agent_id !== auth()->id(), 403);

        $application->load(['service', 'media']);

        return view('agent.applications.show', compact('application'));
    }

    public function cancel(Application $application)
    {
        abort_if($application->agent_id !== auth()->id(), 403);

        if ($application->status === ApplicationStatus::CANCELLED) {
            return back()->with('error', 'Application is already cancelled.');
        }

        $application->update([
            'status' => ApplicationStatus::CANCELLED,
            'commission_amount' => 0,
        ]);

        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Application cancelled by agent');

        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        Notification::send($admins, new ApplicationCancelledNotification($application));

        return back()->with('success', 'Application has been successfully cancelled.');
    }

    public function destroy(Application $application)
    {
        abort_if($application->agent_id !== auth()->id(), 403);

        $application->delete();

        return back()->with('success', 'Application deleted successfully.');
    }

    public function restore($id)
    {
        $application = Application::withTrashed()->findOrFail($id);
        abort_if($application->agent_id !== auth()->id(), 403);

        $application->restore();

        return back()->with('success', 'Application restored successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Secure Document Viewing for Agent
    |--------------------------------------------------------------------------
    */

    public function viewDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);

        if (strtoupper(auth()->user()->role) !== 'AGENT' || $media->model->agent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this document.');
        }

        $path = $media->getPath();

        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        $headers = [];
        if (Str::endsWith(strtolower($media->file_name), '.pdf')) {
            $headers['Content-Type'] = 'application/pdf';
        }

        return response()->file($path, $headers)->setContentDisposition('inline', $media->file_name);
    }

    public function downloadDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);

        if (strtoupper(auth()->user()->role) !== 'AGENT' || $media->model->agent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this document.');
        }

        $path = $media->getPath();

        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path, $media->file_name);
    }

    /*
    |--------------------------------------------------------------------------
    | Balance Sheet Generator (Agent Side)
    |--------------------------------------------------------------------------
    */
    public function balanceSheetForm($id)
    {
        $application = Application::with('media')->findOrFail($id);

        // SECURITY: Ensure the agent owns this application!
        abort_if($application->agent_id !== auth()->id(), 403);

        $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;

        $sales = (float) preg_replace('/[^0-9.]/', '', $formData['business_turnover'] ?? 0);
        $netProfit = (float) preg_replace('/[^0-9.]/', '', $formData['business_income'] ?? $formData['business_profession_income'] ?? 0);
        $otherIncome = (float) preg_replace('/[^0-9.]/', '', $formData['other_income'] ?? 0);

        $extractedData = [];
        $parser = new Parser;

        $compMedia = $application->getFirstMedia('computation_sheet');

        if ($compMedia) {
            try {
                $pdf = $parser->parseFile($compMedia->getPath());
                $text = $pdf->getText();

                if (preg_match('/Inventories\s*\n*\s*([\d,]+\.?\d*)/i', $text, $matches)) {
                    $extractedData['closing_stock'] = (float) str_replace(',', '', $matches[1]);
                }
                if (preg_match('/Sundry debtors\s*\n*\s*([\d,]+\.?\d*)/i', $text, $matches)) {
                    $extractedData['sundry_debtors'] = (float) str_replace(',', '', $matches[1]);
                }
                if (preg_match('/Cash in hand\s*\n*\s*([\d,]+\.?\d*)/i', $text, $matches)) {
                    $extractedData['cash_in_hand'] = (float) str_replace(',', '', $matches[1]);
                }
                if (preg_match('/Sundry Creditors\s*\n*\s*([\d,]+\.?\d*)/i', $text, $matches)) {
                    $extractedData['sundry_creditors'] = (float) str_replace(',', '', $matches[1]);
                }
                if (preg_match('/Gross Receipts\/Turnover\s*\n*\s*([\d,]+\.?\d*)/i', $text, $matches)) {
                    $sales = (float) str_replace(',', '', $matches[1]);
                }
                if (preg_match('/Net Profit Declared\s*\n*\s*([\d,]+\.?\d*)/i', $text, $matches) || preg_match('/Total Income\s*\n*\s*([\d,]+\.?\d*)/i', $text, $matches)) {
                    $foundProfit = (float) str_replace(',', '', $matches[1]);
                    if ($foundProfit > 0) {
                        $netProfit = $foundProfit;
                    }
                }

            } catch (\Exception $e) {
            }
        }

        return view('agent.applications.balance_sheet', compact('application', 'sales', 'netProfit', 'otherIncome', 'extractedData'));
    }

    public function generateBalanceSheetPdf(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        // SECURITY: Ensure the agent owns this application
        abort_if($application->agent_id !== auth()->id(), 403);

        $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;
        $applicantName = strtoupper($formData['applicant_name'] ?? 'APPLICANT NAME');
        $panNumber = strtoupper($formData['pan_number'] ?? 'PAN NOT PROVIDED');

        $data = [];
        foreach ($request->except('_token') as $key => $value) {
            $data[$key] = (float) $value ?: 0;
        }

        $grossProfit = ($data['sales'] + $data['closing_stock']) - ($data['opening_stock'] + $data['purchases'] + $data['direct_expenses']);
        $tradingTotal = $data['sales'] + $data['closing_stock'];
        $totalIndirectExp = $data['salaries'] + $data['electricity'] + $data['shop_rent'] + $data['telephone_internet'] + $data['printing_stationery'] + $data['repairs_maintenance'] + $data['interest_on_loan'] + $data['other_expenses'];
        $totalIndirectInc = $data['interest_income'] + $data['other_income'];
        $netProfit = ($grossProfit + $totalIndirectInc) - $totalIndirectExp;
        $pnlTotal = $totalIndirectExp + $netProfit;
        $closingCapital = $data['opening_capital'] + $netProfit - $data['drawings'];
        $capitalTotal = $data['opening_capital'] + $netProfit;
        $bsTotal = $closingCapital + $data['bank_loan'] + $data['other_loans'] + $data['sundry_creditors'] + $data['other_current_liabilities'];

        $pdfData = compact('applicantName', 'panNumber', 'data', 'grossProfit', 'netProfit', 'closingCapital', 'tradingTotal', 'pnlTotal', 'bsTotal', 'capitalTotal');

        // Generate PDF using your template
        $pdf = Pdf::loadView('admin.applications.pdfs.balance_sheet', $pdfData);

        // --- NEW: AUTO-SAVE TO DATABASE FOR AGENT ---
        $fileName = 'Balance_Sheet_'.$panNumber.'.pdf';

        // Clear any old balance sheets for this app so they don't pile up
        $application->clearMediaCollection('balance_sheet');

        // Save the raw PDF string to the private folder
        $application->addMediaFromString($pdf->output())
            ->usingFileName($fileName)
            ->usingName('Balance Sheet')
            ->toMediaCollection('balance_sheet', 'private');
        // ----------------------------------

        return $pdf->stream($fileName);
    }
}
