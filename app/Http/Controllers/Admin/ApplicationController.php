<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ApplicationsExport;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Smalot\PdfParser\Parser;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'other');
        $pageTitle = match ($type) {
            'gst-return-filing' => 'GST Return Filing Applications',
            'itr-filing' => 'ITR Filing Applications',
            'gst-registration' => 'GST Registration Applications',
            'incomplete' => 'Incomplete & Abandoned Apps',
            default => 'Other Applications',
        };

        $services = Service::where('active', true)->get();
        $agents = User::where('role', 'agent')->get();

        // --- NEW DYNAMIC KPI LOGIC ---

        $query = Application::with(['service', 'agent']);

        if ($type === 'incomplete') {
            $query->where(function ($q) {
                $q->whereIn('status', ['DRAFT', 'CANCELLED', 'FAILED'])
                    ->orWhereIn('payment_status', ['FAILED', 'PENDING']);
            })->whereNotIn('status', ['SUBMITTED', 'IN_PROGRESS', 'E_FILING', 'OTP_VERIFICATION', 'COMPLETED']);
        } else {
            $query->whereNotIn('status', ['DRAFT', 'CANCELLED', 'FAILED'])
                ->where('payment_status', '!=', 'FAILED');
        }

        $specialSlugs = ['itr-filing', 'gst-registration', 'gst-return-filing'];

        if ($type !== 'incomplete') {
            if ($type === 'other') {
                $query->whereHas('service', function ($q) use ($specialSlugs) {
                    $q->whereNotIn('slug', $specialSlugs);
                });
            } elseif (in_array($type, $specialSlugs)) {
                $query->whereHas('service', function ($q) use ($type) {
                    $q->where('slug', $type);
                });
            }
        }

        $stats = $query->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status != 'COMPLETED' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN payment_status = 'FAILED' THEN 1 ELSE 0 END) as failed
        ")->first();

        // -----------------------------

        // Exclude 'Super Admin' and 'Rahul Sharma' since they are not operators
        $teamMembers = User::whereIn('role', ['TEAM', 'team', 'ADMIN', 'admin'])
            ->where('is_active', true)
            ->whereNotIn('name', ['Super Admin', 'Rahul Sharma'])
            ->get();

        return view('admin.applications.index', compact(
            'services', 'agents', 'stats', 'type', 'pageTitle', 'teamMembers'
        ));
    }

    public function data(Request $request)
    {
        // 2. FORM DATA EXPORTS
        $query = Application::with([
            'service',
            'agent',
            'media' => function ($q) {
                $q->whereIn('collection_name', ['itr_acknowledgement', 'computation_sheet', 'balance_sheet']);
            },
        ]);

        $type = $request->type ?? 'other';

        // ==========================================
        //  BUG FIX: THE BLACK HOLE FILTER
        //
        // ==========================================
        if ($type === 'incomplete') {
            // "Incomplete" means it is either explicitly marked as Draft/Cancelled/Failed
            // OR (it is NOT completed AND its payment is Pending/Failed)
            $query->where(function ($q) {
                $q->whereIn('status', ['DRAFT', 'CANCELLED', 'FAILED'])
                    ->orWhere(function ($subQ) {
                        $subQ->whereIn('payment_status', ['FAILED', 'PENDING'])
                            ->where('status', '!=', 'COMPLETED'); // If it is COMPLETED, do NOT put it in Incomplete!
                    });
            });
        } else {
            // For all standard service tabs (ITR, GST, etc) AND the Completed tab:
            // Just show it as long as it isn't explicitly a Draft/Cancelled/Failed.
            // We NO LONGER hide Pending payments here if the Admin forced it to Completed!
            $query->whereNotIn('status', ['DRAFT', 'CANCELLED', 'FAILED']);
        }

        $specialSlugs = ['itr-filing', 'gst-registration', 'gst-return-filing'];

        if ($type !== 'incomplete') {
            if ($type === 'other') {
                $query->whereHas('service', function ($q) use ($specialSlugs) {

                    $q->whereNotIn('slug', $specialSlugs);
                });
            } elseif (in_array($type, $specialSlugs)) {
                $query->whereHas('service', function ($q) use ($type) {
                    $q->where('slug', $type);
                });
            }
        }

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

        if ($request->is_trashed == 'true') {
            $query->onlyTrashed();
        }

        // Exclude 'Super Admin' and 'Rahul Sharma' since they are not operators
        $teamMembers = User::whereIn('role', ['TEAM', 'team', 'ADMIN', 'admin'])
            ->where('is_active', true)
            ->whereNotIn('name', ['Super Admin', 'Rahul Sharma'])
            ->get();

        return datatables()->of($query)
            ->filterColumn('agent', function ($query, $keyword) {
                $query->whereHas('agent', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('service', function ($query, $keyword) {
                $query->whereHas('service', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('dynamic_data', function ($query, $keyword) {
                $query->where('form_data', 'like', "%{$keyword}%");
            })
            ->addColumn('dynamic_data', function ($a) {
                $targetField = $a->service->primary_data_field ?? null;
                if (! $targetField || empty($a->form_data)) {
                    return '<span class="text-muted text-xs font-italic">N/A</span>';
                }
                $formData = is_string($a->form_data) ? json_decode($a->form_data, true) : $a->form_data;
                $value = $formData[$targetField] ?? null;
                if (! $value) {
                    return '<span class="text-muted text-xs font-italic">N/A</span>';
                }
                $displayVal = is_array($value) ? implode(', ', $value) : (string) $value;

                return '<span class="font-weight-bold text-dark">'.e($displayVal).'</span>';
            })
            // 1. ACK NUMBER COLUMN
            ->addColumn('ack_no', function ($a) {
                if ($a->service->slug !== 'itr-filing') {
                    return '-';
                }

                $ackMedia = $a->getFirstMedia('itr_acknowledgement');

                if ($ackMedia) {
                    $downloadUrl = route('admin.documents.download', $ackMedia->id);
                    $ackNumber = $ackMedia->getCustomProperty('ack_number');

                    if (! $ackNumber) {
                        try {
                            $parser = new Parser;
                            $pdf = $parser->parseFile($ackMedia->getPath());
                            $text = substr($pdf->getText(), 0, 2000);

                            if (preg_match('/\b(\d{15})\b/', $text, $matches)) {
                                $ackNumber = $matches[1];
                                $ackMedia->setCustomProperty('ack_number', $ackNumber);
                                $ackMedia->save();
                            }
                        } catch (\Exception $e) {
                        }
                    }

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
                    $downloadUrl = route('admin.documents.download', $compMedia->id);

                    return '<a href="'.$downloadUrl.'" class="text-primary font-weight-bold"><i class="fas fa-download mr-1"></i> Download</a>';
                }

                return '<span class="text-muted text-xs font-italic">Pending</span>';
            })
            // 3. NEW SMART BALANCE SHEET GENERATOR
            ->addColumn('balance_sheet', function ($a) {
                if ($a->service->slug !== 'itr-filing') {
                    return '-';
                }

                $bsMedia = $a->getFirstMedia('balance_sheet');

                if ($bsMedia) {
                    $viewUrl = route('admin.documents.view', $bsMedia->id);
                    $downloadUrl = route('admin.documents.download', $bsMedia->id);
                    $regenUrl = route('admin.applications.balance-sheet', $a->id);

                    // Show View, Download, and a tiny Regenerate button
                    return '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.$viewUrl.'" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1" title="View"><i class="fas fa-eye"></i></a>
                        <a href="'.$downloadUrl.'" class="btn btn-sm btn-outline-success px-2 py-1" title="Download"><i class="fas fa-download"></i></a>
                        <a href="'.$regenUrl.'" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Regenerate"><i class="fas fa-sync-alt"></i></a>
                    </div>';
                }

                $url = route('admin.applications.balance-sheet', $a->id);

                return '<a href="'.$url.'" class="btn btn-sm btn-outline-success font-weight-bold" style="white-space: nowrap;"><i class="fas fa-file-excel mr-1"></i> Generate</a>';
            })
            ->addColumn('checkbox', fn ($a) => '<input type="checkbox" class="row-select" value="'.$a->id.'">')
            ->addColumn('agent', fn ($a) => $a->agent->name ?? 'N/A')
            ->addColumn('service', fn ($a) => $a->service->name ?? 'N/A')
            ->addColumn('status', fn ($a) => '<span class="badge badge-info">'.($a->status->value ?? $a->status).'</span>')
            ->addColumn('payment', fn ($a) => '<span class="badge badge-success">'.($a->payment_status->value ?? $a->payment_status).'</span>')
            ->addColumn('amount', fn ($a) => '₹'.number_format($a->amount, 2))
            ->addColumn('date', fn ($a) => $a->created_at->format('d M Y'))
            ->addColumn('assign_to', function ($a) use ($teamMembers) {
                $html = '<select class="form-select form-select-sm d-inline-block assign-team-select"  data-app-id="'.$a->id.'" style="border-radius: 6px; font-size: 0.85rem; height: 31px; border: 1px solid #cbd5e1; outline: none; min-width: 150px;">';
                $html .= '<option value="">Unassigned</option>';
                foreach ($teamMembers as $member) {
                    $selected = $a->assigned_to == $member->id ? 'selected' : '';
                    $html .= '<option value="'.e($member->id).'" '.$selected.'>'.e($member->name).'</option>';
                }
                $html .= '</select>';

                return $html;
            })
            ->addColumn('actions', function ($a) use ($request) {
                if ($request->is_trashed == 'true') {
                    return '
                        <form action="'.route('admin.applications.restore', $a->id).'" method="POST" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent(\'confirm-action\', { detail: { form: this, title: \'Restore Application?\', message: \'Are you sure you want to restore this application?\' } }));" style="display:inline;">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-success">Restore</button>
                        </form>
                    ';
                }

                $html = '<a href="'.route('admin.applications.show', $a).'" class="btn btn-sm btn-primary">View</a>';
                $html .= '
                    <form action="'.route('admin.applications.destroy', $a->id).'" method="POST" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent(\'confirm-action\', { detail: { form: this, title: \'Move to trash?\', message: \'Are you sure you want to delete this application?\' } }));" style="display:inline; margin-left: 4px;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.25rem 0.5rem;"><i class="fas fa-trash"></i></button>
                    </form>
                ';

                return $html;
            })
            ->rawColumns(['checkbox', 'dynamic_data', 'status', 'payment', 'ack_no', 'computation', 'balance_sheet', 'assign_to', 'actions'])
            ->make(true);
    }

    public function export(Request $request)
    {
        $filter = $request->query('filter');

        // 1. STANDARD EXPORT (Using existing Maatwebsite logic)
        if (! $filter) {
            return Excel::download(
                new ApplicationsExport,
                'applications.xlsx'
            );
        }

        // 2. FORM DATA EXPORTS
        // 2. FORM DATA EXPORTS
        $query = Application::with(['agent', 'service'])
            ->whereHas('service', function ($q) {
                $q->where('name', 'ITR Filing (Individual / Business)');
            })
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'FAILED'])
            ->where('payment_status', '!=', 'FAILED');

        if ($filter === 'completed_forms') {
            $query->where('status', 'COMPLETED');
            $fileName = 'ITR_Completed_Form_Data.csv';
        } elseif ($filter === 'pending_only') {
            $query->where('status', '!=', 'COMPLETED');
            $fileName = 'ITR_Pending_Form_Data.csv';
        } else {
            return back()->with('error', 'Invalid export filter.');
        }

        $applications = $query->get();

        if ($applications->isEmpty()) {
            return back()->with('error', 'No ITR applications found for this filter.');
        }

        $dynamicKeys = [];
        foreach ($applications as $app) {
            $formData = is_string($app->form_data) ? json_decode($app->form_data, true) : $app->form_data;
            if (is_array($formData)) {
                foreach (array_keys($formData) as $key) {
                    if (! in_array($key, $dynamicKeys)) {
                        $dynamicKeys[] = $key;
                    }
                }
            }
        }

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $standardColumns = ['App ID', 'Agent Name', 'Service', 'Status', 'Submitted Date'];
        $displayDynamicKeys = array_map(function ($key) {
            return Str::title(str_replace('_', ' ', $key));
        }, $dynamicKeys);

        $csvHeaders = array_merge($standardColumns, $displayDynamicKeys);

        $callback = function () use ($applications, $csvHeaders, $dynamicKeys) {
            $file = fopen('php://output', 'w');

            fwrite($file, $bom = (chr(0xEF).chr(0xBB).chr(0xBF)));
            fputcsv($file, $csvHeaders);

            foreach ($applications as $app) {
                $formData = is_string($app->form_data) ? json_decode($app->form_data, true) : $app->form_data;
                if (! is_array($formData)) {
                    $formData = [];
                }

                $statusValue = is_object($app->status) ? $app->status->value : $app->status;
                $row = [
                    $app->id,
                    $app->agent->name ?? 'N/A',
                    $app->service->name ?? 'N/A',
                    $statusValue,
                    $app->created_at->format('d M Y h:i A'),
                ];

                foreach ($dynamicKeys as $key) {
                    $value = $formData[$key] ?? '';
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    } elseif (is_bool($value)) {
                        $value = $value ? 'Yes' : 'No';
                    }
                    $row[] = (string) $value;
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:applications,id',
        ]);

        Application::whereIn('id', $request->ids)
            ->update(['status' => 'IN_PROGRESS']);

        return back()->with('success', 'Applications updated.');
    }

    public function show(Application $application)
    {
        $application->load(['service', 'agent']);

        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|string|in:IN_PROGRESS,E_FILING,OTP_VERIFICATION,COMPLETED',
        ]);

        DB::transaction(function () use ($application, $request) {
            $application->update(['status' => $request->status]);

            if ($request->status === 'COMPLETED') {
                $application->update(['completed_at' => now()]);
            }

            activity('application')
                ->performedOn($application)
                ->causedBy(auth()->user())
                ->log('Status updated to '.$request->status);
        });

        if ($request->status === 'COMPLETED') {
            // --- DYNAMIC EMAIL AUTOMATION ---
            $emailKey = $application->service->applicant_email_field;

            if (! empty($application->form_data)) {
                $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;

                // SMART FALLBACK: If emailKey is missing in DB, try common email fields
                $clientEmail = (! empty($emailKey) && isset($formData[$emailKey]))
                    ? $formData[$emailKey]
                    : ($formData['email'] ?? $formData['email_id'] ?? $formData['applicant_email'] ?? null);

                $clientName = $formData['applicant_name'] ?? $formData['name'] ?? $formData['full_name'] ?? $formData['company_name'] ?? $formData['firm_name'] ?? 'Valued Client';
                $trackingUrl = URL::signedRoute('tracking.show', ['application' => $application->id]);

                if ($clientEmail && filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
                    try {
                        Mail::send('emails.application_completed', [
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

    public function uploadDocument(Request $request, Application $application)
    {
        $request->validate([
            'document' => 'nullable|file|mimetypes:application/pdf,application/octet-stream,image/jpeg,image/png|extensions:pdf,png,jpg,jpeg|max:5120',
            'ack_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream,image/jpeg,image/png|extensions:pdf,png,jpg,jpeg|max:5120',
            'computation_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream|extensions:pdf|max:5120',
            'moa_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|extensions:pdf,doc,docx|max:5120',
            'aoa_file' => 'nullable|file|mimetypes:application/pdf,application/octet-stream,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|extensions:pdf,doc,docx|max:5120',
        ]);

        $uploaded = false;

        if ($request->hasFile('document')) {
            $application->addMediaFromRequest('document')->toMediaCollection('documents', 'private');
            $uploaded = true;
        }

        if ($request->hasFile('ack_file')) {
            $media = $application->addMediaFromRequest('ack_file')->toMediaCollection('itr_acknowledgement', 'private');
            try {
                $parser = new Parser;
                $pdf = $parser->parseFile($media->getPath());
                $text = substr($pdf->getText(), 0, 2000);
                if (preg_match('/\b(\d{15})\b/', $text, $matches)) {
                    $media->setCustomProperty('ack_number', $matches[1]);
                    $media->save();
                }
            } catch (\Exception $e) {
            }

            $uploaded = true;
        }

        if ($request->hasFile('computation_file')) {
            $application->addMediaFromRequest('computation_file')->toMediaCollection('computation_sheet', 'private');
            $uploaded = true;
        }

        // NEW: Handle Draft MOA Upload
        if ($request->hasFile('moa_file')) {
            $application->clearMediaCollection('moa_document'); // Clear old one if replacing
            $application->addMediaFromRequest('moa_file')->toMediaCollection('moa_document', 'private');
            $uploaded = true;
        }

        // NEW: Handle Draft AOA Upload
        if ($request->hasFile('aoa_file')) {
            $application->clearMediaCollection('aoa_document'); // Clear old one if replacing
            $application->addMediaFromRequest('aoa_file')->toMediaCollection('aoa_document', 'private');
            $uploaded = true;
        }

        if (! $uploaded) {
            return back()->with('error', 'Please select a file to upload.');
        }

        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Uploaded supporting document(s)');

        return back()->with('success', 'Document(s) uploaded successfully.');
    }

    public function storeCredentials(Request $request, Application $application)
    {
        $request->validate([
            'admin_username' => 'nullable|string|max:255',
            'admin_password' => 'nullable|string|max:255',
            'final_document' => 'nullable|file|mimetypes:application/pdf,image/jpeg,image/png|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Safely decode JSON if it comes out as a string
        $formData = is_string($application->form_data)
            ? json_decode($application->form_data, true)
            : ($application->form_data ?? []);

        // Save GST Credentials — password is encrypted at rest
        if ($request->has('admin_username')) {
            $formData['admin_username'] = $request->admin_username;
        }
        if ($request->has('admin_password') && ! empty($request->admin_password)) {
            $formData['admin_password'] = Crypt::encryptString($request->admin_password);
        }

        // Update the database
        $application->update(['form_data' => $formData]);

        // Handle File Upload with Smart Labeling
        if ($request->hasFile('final_document')) {
            // Check if it is a company service based on the slug
            $companyServices = ['fpo-registration', 'section-8-company', 'llp-registration', 'opc-registration', 'private-limited-company-registration'];
            $isCompanySetup = in_array($application->service->slug ?? '', $companyServices);

            // Automatically name the file correctly
            $certLabel = $isCompanySetup ? 'Incorporation Certificate' : 'GST Certificate';

            $application->addMediaFromRequest('final_document')
                ->withCustomProperties(['label' => $certLabel])
                ->toMediaCollection('final_deliverables', 'private');
        }

        // Log the activity
        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Updated application credentials and deliverables');

        return back()->with('success', 'Credentials and deliverables saved successfully.');
    }

    public function deleteDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $media->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    public function updatePaymentStatus(Request $request, Application $application)
    {
        $request->validate([
            'payment_status' => 'required|string|in:REFUNDED',
        ]);

        $application->update(['payment_status' => $request->payment_status]);

        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Payment status updated to '.$request->payment_status);

        return back()->with('success', 'Payment status updated successfully.');
    }

    public function viewDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
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
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $path = $media->getPath();
        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path, $media->file_name);
    }

    public function exportSingle(Application $application)
    {
        $formData = $application->form_data ?? [];
        $fileName = 'Application_'.$application->id.'_Client_Data.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Field Name', 'Provided Value'];

        $callback = function () use ($formData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($formData as $key => $value) {
                $displayKey = Str::title(str_replace('_', ' ', $key));
                $displayValue = $value;
                if (is_array($value)) {
                    $displayValue = implode(', ', $value);
                } elseif (is_bool($value)) {
                    $displayValue = $value ? 'Yes' : 'No';
                }
                fputcsv($file, [$displayKey, $displayValue]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function balanceSheetForm($id)
    {
        $application = Application::with('media')->findOrFail($id);

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

        return view('admin.applications.balance_sheet', compact('application', 'sales', 'netProfit', 'otherIncome', 'extractedData'));
    }

    public function generateBalanceSheetPdf(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;
        $applicantName = strtoupper($formData['applicant_name'] ?? 'APPLICANT NAME');
        $panNumber = strtoupper($formData['pan_number'] ?? 'PAN NOT PROVIDED');

        $data = [];
        foreach ($request->except('_token') as $key => $value) {
            $data[$key] = (float) $value ?: 0;
        }

        $grossProfit = ($data['sales'] + $data['closing_stock']) - ($data['opening_stock'] + $data['purchases'] + $data['direct_expenses']);
        $tradingTotal = $data['sales'] + $data['closing_stock'];

        $totalIndirectExp = $data['salaries'] + $data['electricity'] + $data['shop_rent'] + $data['telephone_internet'] +
                            $data['printing_stationery'] + $data['repairs_maintenance'] + $data['interest_on_loan'] + $data['other_expenses'];

        $totalIndirectInc = $data['interest_income'] + $data['other_income'];
        $netProfit = ($grossProfit + $totalIndirectInc) - $totalIndirectExp;
        $pnlTotal = $totalIndirectExp + $netProfit;

        $closingCapital = $data['opening_capital'] + $netProfit - $data['drawings'];
        $capitalTotal = $data['opening_capital'] + $netProfit;

        $bsTotal = $closingCapital + $data['bank_loan'] + $data['other_loans'] + $data['sundry_creditors'] + $data['other_current_liabilities'];

        $pdfData = compact(
            'applicantName', 'panNumber', 'data',
            'grossProfit', 'netProfit', 'closingCapital',
            'tradingTotal', 'pnlTotal', 'bsTotal', 'capitalTotal'
        );

        $pdf = Pdf::loadView('admin.applications.pdfs.balance_sheet', $pdfData);

        // --- NEW: AUTO-SAVE TO DATABASE ---
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

    public function assignTeam(Request $request, $id)
    {
        $request->validate([
            'team_id' => 'nullable|integer|exists:users,id',
        ]);

        $application = Application::findOrFail($id);

        if ($request->team_id) {
            $teamMember = User::whereIn('role', ['TEAM', 'team'])->findOrFail($request->team_id);
            $assignTo = $teamMember->id;
        } else {
            $assignTo = null;
        }

        $application->update(['assigned_to' => $assignTo]);

        return response()->json([
            'success' => true,
            'message' => 'Application assigned successfully!',
        ]);
    }

    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()->back()->with('success', 'Application moved to trash successfully.');
    }

    public function restore($id)
    {
        $application = Application::onlyTrashed()->findOrFail($id);
        $application->restore();

        return redirect()->back()->with('success', 'Application restored successfully.');
    }
}
