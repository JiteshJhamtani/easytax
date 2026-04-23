<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApplicationsExport;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Http;


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
            
            // 1. Teach DataTables how to search the Agent relationship
            ->filterColumn('agent', function($query, $keyword) {
                $query->whereHas('agent', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            
            // 2. Teach DataTables how to search the Service relationship
            ->filterColumn('service', function($query, $keyword) {
                $query->whereHas('service', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            // 3. THE NEW DYNAMIC DATA COLUMN
            ->addColumn('dynamic_data', function($a) {
                $targetField = $a->service->primary_data_field;
                if (!$targetField || empty($a->form_data)) {
                    return '<span class="text-muted text-xs font-italic">N/A</span>';
                }
                
                $formData = is_string($a->form_data) ? json_decode($a->form_data, true) : $a->form_data;
                $value = $formData[$targetField] ?? null;
                
                if (!$value) return '<span class="text-muted text-xs font-italic">N/A</span>';
                
                $displayVal = is_array($value) ? implode(', ', $value) : (string) $value;
                return '<span class="font-weight-bold text-dark">' . $displayVal . '</span>';
            })  

            ->addColumn('checkbox', fn($a) => '
                <input type="checkbox" class="row-select" value="' . $a->id . '">
            ')
            ->addColumn('agent', fn($a) => $a->agent->name ?? 'N/A')
            ->addColumn('service', fn($a) => $a->service->name ?? 'N/A')
            ->addColumn('status', fn($a) => '<span class="badge badge-info">' . ($a->status->value ?? $a->status) . '</span>')
            ->addColumn('payment', fn($a) => '<span class="badge badge-success">' . ($a->payment_status->value ?? $a->payment_status) . '</span>')
            ->addColumn('amount', fn($a) => '₹' . number_format($a->amount, 2))
            ->addColumn('date', fn($a) => $a->created_at->format('d M Y'))
            ->addColumn('actions', function ($a) {
                return '
                <a href="' . route('admin.applications.show', $a) . '"
                class="btn btn-sm btn-primary">
                View
                </a>';
            })
            ->rawColumns(['checkbox', 'dynamic_data', 'status', 'payment', 'actions'])
            ->make(true);
    }



   public function export(Request $request)
    {
        $filter = $request->query('filter');

        // 1. STANDARD EXPORT (Using existing Maatwebsite logic)
        if (!$filter) {
            return Excel::download(
                new ApplicationsExport(),
                'applications.xlsx'
            );
        }

        // 2. FORM DATA EXPORTS 
        // Force the query to ONLY fetch apps where the related Service name matches exactly
        $query = Application::with(['agent', 'service'])
            ->whereHas('service', function ($q) {
                $q->where('name', 'ITR Filing (Individual / Business)');
            });

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

        // STEP A: Gather every unique dynamic key from ALL form_data across these apps
        $dynamicKeys = [];
        foreach ($applications as $app) {
            $formData = is_string($app->form_data) ? json_decode($app->form_data, true) : $app->form_data;
            if (is_array($formData)) {
                foreach (array_keys($formData) as $key) {
                    if (!in_array($key, $dynamicKeys)) {
                        $dynamicKeys[] = $key;
                    }
                }
            }
        }

        // STEP B: Prepare HTTP Headers for CSV Download
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // STEP C: Define Base Columns + Cleaned Dynamic Columns
        $standardColumns = ['App ID', 'Agent Name', 'Service', 'Status', 'Submitted Date'];
        $displayDynamicKeys = array_map(function($key) {
            return \Illuminate\Support\Str::title(str_replace('_', ' ', $key));
        }, $dynamicKeys);
        
        $csvHeaders = array_merge($standardColumns, $displayDynamicKeys);

        // STEP D: Stream the CSV Output dynamically
        $callback = function() use($applications, $csvHeaders, $dynamicKeys) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to ensure Excel reads special characters correctly
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            
            fputcsv($file, $csvHeaders);

            foreach ($applications as $app) {
                // Ensure form_data is an array
                $formData = is_string($app->form_data) ? json_decode($app->form_data, true) : $app->form_data;
                if (!is_array($formData)) {
                    $formData = [];
                }

                // 1. Build standard data
                $statusValue = is_object($app->status) ? $app->status->value : $app->status;
                $row = [
                    $app->id,
                    $app->agent->name ?? 'N/A',
                    $app->service->name ?? 'N/A',
                    $statusValue,
                    $app->created_at->format('d M Y h:i A')
                ];

                // 2. Build dynamic form data (match exact order of headers)
                foreach ($dynamicKeys as $key) {
                    $value = $formData[$key] ?? '';
                    
                    // Handle arrays and booleans cleanly
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

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|string|in:IN_PROGRESS,E_FILING,OTP_VERIFICATION,COMPLETED',
        ]);

        $application->update(['status' => $request->status]);

        if ($request->status === 'COMPLETED') {
            $application->update(['completed_at' => now()]);
        }

        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Status updated to ' . $request->status);

        return back()->with('success', 'Application status updated successfully.');
    }

    public function uploadDocument(Request $request, Application $application)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120', // 5MB limit
        ]);

        // Spatie magic: Uploads to private storage and links to this application instantly
        $application->addMediaFromRequest('document')
                   ->toMediaCollection('documents', 'private');

        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Uploaded a supporting document');

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function storeCredentials(Request $request, Application $application)
    {
        $request->validate([
            'admin_username' => 'nullable|string', // Changed from admin_otp
            'admin_password' => 'nullable|string',
            'final_document' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
        ]);

        // 1. Safely inject Username and Password into the existing form_data array
        $formData = $application->form_data ?? [];
        if ($request->has('admin_username')) {
            $formData['admin_username'] = $request->admin_username;
        }
        if ($request->has('admin_password')) {
            $formData['admin_password'] = $request->admin_password;
        }
        $application->update(['form_data' => $formData]);

        // 2. Safely store the Final Document
        if ($request->hasFile('final_document')) {
            $application->addMediaFromRequest('final_document')
                ->withCustomProperties(['label' => 'GST Certificate']) // Changed label
                ->toMediaCollection('final_deliverables', 'private');
        }

        activity('application')
            ->performedOn($application)
            ->causedBy(auth()->user())
            ->log('Updated application credentials and deliverables');

        return back()->with('success', 'Credentials and deliverables saved successfully.');
    }

   public function deleteDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);

        // Safely check role regardless of uppercase or lowercase
        if (strtoupper(auth()->user()->role) !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        // Delete from storage and database
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
            ->log('Payment status updated to ' . $request->payment_status);

        return back()->with('success', 'Payment status updated successfully.');
    }

    public function viewDocument($mediaId)
    {
        $media = Media::findOrFail($mediaId);

        // 🔐 Simple security (no policy needed)
        if (auth()->user()->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
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

        if (auth()->user()->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $path = storage_path('app/private/' . $media->id . '/' . $media->file_name);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path, $media->file_name);
    }

    public function exportSingle(Application $application)
    {
        $formData = $application->form_data ?? [];
        $fileName = 'Application_' . $application->id . '_Client_Data.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Field Name', 'Provided Value'];

        $callback = function() use($formData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // Write the header row

            foreach ($formData as $key => $value) {
                // Clean up the key (e.g., 'pan_number' becomes 'Pan Number')
                $displayKey = \Illuminate\Support\Str::title(str_replace('_', ' ', $key));
                
                // Clean up the value (Handle arrays and booleans)
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
 
}


