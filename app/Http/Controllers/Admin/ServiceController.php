<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Services List 
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('admin.services.index');
    }

   public function datatable()
    {
        $services = Service::query()
            ->withCount(['applications' => function ($query) {
                // Hide Draft, Failed, and Canceled from the total count
                $query->whereNotIn('status', ['draft', 'failed', 'canceled']);
            }]);
        return DataTables::of($services)
            
            // Map the calculated count so the frontend can read it
            ->addColumn('applications', function ($service) {
                return $service->applications_count;
            })

            ->addColumn('commission_display', function ($service) {
                if ($service->commission_type === 'percentage') {
                    return $service->commission_value.'%';
                }

                return '₹'.number_format($service->commission_value, 2);
            })

            // THE FIX: Stop Laravel from running SQL text searches on these virtual columns
            ->filterColumn('applications', function($query, $keyword) { /* Do nothing */ })
            ->filterColumn('commission_display', function($query, $keyword) { /* Do nothing */ })

            ->addColumn('action', function ($service) {
                $toggle = $service->active ? 'Deactivate' : 'Activate';

                return '
                    <a href="'.route('admin.services.show', $service).'"
                       class="btn btn-sm btn-primary">View</a>

                    <a href="'.route('admin.services.edit', $service).'"
                       class="btn btn-sm btn-warning">Edit</a>

                    <form method="POST"
                          action="'.route('admin.services.toggle-status', $service).'"
                          style="display:inline;">
                        '.csrf_field().method_field('PATCH').'
                        <button class="btn btn-sm btn-danger">'.$toggle.'</button>
                    </form>
                ';
            })

            ->rawColumns(['action'])

            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Service
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.services.create');
    }

  public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        
        // Safely catch the new dynamic field bypassing strict validation
        $data['primary_data_field'] = $request->input('primary_data_field');
        $data['whatsapp_number_field'] = $request->input('whatsapp_number_field'); // <-- ADD THIS LINE
        $data['sort_order'] = $request->input('sort_order', 0); // <-- ADD THIS LINE

        $formSchema = $this->parseFormSchema($data);
        unset($data['form_schema']);

        $service = Service::create($data);

        if ($formSchema) {
            $this->writeFormConfig($service->slug, $formSchema);
        }

        return redirect()
            ->route('admin.services.show', $service)
            ->with('success', 'Service created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Service
    |--------------------------------------------------------------------------
    */

    public function show(Service $service)
    {
        $formConfig = config("service_forms.{$service->slug}");

       $stats = [
            'total_applications' => $service->applications()
                ->whereNotIn('status', ['draft', 'failed', 'canceled'])
                ->count(),
            'total_revenue' => $service->applications()
                ->whereNotIn('status', ['draft', 'failed', 'canceled'])
                ->sum('amount'),
        ];

        return view('admin.services.show', compact('service', 'formConfig', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Service
    |--------------------------------------------------------------------------
    */

    public function edit(Service $service)
    {
        $formConfig = config("service_forms.{$service->slug}");

        return view('admin.services.edit', compact('service', 'formConfig'));
    }

 public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        
        // 🚨 THE FIX: Forcefully grab the JSON data directly from the raw request
        $data['form_schema'] = $request->input('form_schema');
        
        // Safely catch the new dynamic fields
        $data['primary_data_field'] = $request->input('primary_data_field');
        $data['whatsapp_number_field'] = $request->input('whatsapp_number_field'); 
        $data['applicant_email_field'] = $request->input('applicant_email_field'); 
        $data['sort_order'] = $request->input('sort_order', 0); 

        $formSchema = $this->parseFormSchema($data);
        unset($data['form_schema']);

        $oldSlug = $service->slug;

        $service->update($data);

        if ($formSchema) {
            if ($oldSlug !== $service->slug) {
                $this->removeFormConfig($oldSlug);
            }
            $this->writeFormConfig($service->slug, $formSchema);
        }

        // ==========================================
        // SAVE ENTERPRISE PRICING MATRIX
        // ==========================================
        if ($request->has('pricing_rules')) {
            $service->pricingRules()->delete(); 
            
            foreach ($request->pricing_rules as $rule) {
                if (isset($rule['base_price']) && $rule['base_price'] !== null) { 
                    $service->pricingRules()->create([
                        // GST Fields
                        'gst_type'          => empty($rule['gst_type']) ? null : $rule['gst_type'],
                        'turnover'          => empty($rule['turnover']) ? null : $rule['turnover'],
                        'frequency'         => empty($rule['frequency']) ? null : $rule['frequency'],
                        'plan'              => empty($rule['plan']) ? null : $rule['plan'],
                        
                        // ITR Fields (NEW)
                        'itr_type'          => empty($rule['itr_type']) ? null : $rule['itr_type'],
                        'user_type'         => empty($rule['user_type']) ? null : $rule['user_type'],
                        'itr_salary'        => empty($rule['itr_salary']) ? null : $rule['itr_salary'],
                        'itr_business'      => empty($rule['itr_business']) ? null : $rule['itr_business'],
                        'itr_capital_gains' => empty($rule['itr_capital_gains']) ? null : $rule['itr_capital_gains'],
                        'itr_50l'           => empty($rule['itr_50l']) ? null : $rule['itr_50l'],

                        // Pricing Math
                        'base_price'        => $rule['base_price'],
                        'commission_amount' => empty($rule['commission_amount']) ? 0 : $rule['commission_amount'],
                    ]);
                }
            }
        }
        // ==========================================

        return redirect()
            ->route('admin.services.show', $service)
            ->with('success', 'Service updated successfully.');
    }
    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(Service $service)
    {
        $service->active = ! $service->active;
        $service->save();

        return back()->with('success', 'Service status updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Config File Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Parse the form_schema JSON string from the request.
     */
    private function parseFormSchema(array $data): ?array
    {
        if (empty($data['form_schema'])) {
            return null;
        }

        $schema = json_decode($data['form_schema'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $schema;
    }

    /**
     * Write or update a service's form config in service_forms.php.
     */
    private function writeFormConfig(string $slug, array $schema): void
    {
        $configPath = config_path('service_forms.php');

        $config = file_exists($configPath)
            ? include $configPath
            : [];

        $config[$slug] = $schema;

        $content = "<?php\n\nreturn ".$this->arrayToPhp($config).";\n";

        file_put_contents($configPath, $content);
    }

    /**
     * Remove a service's form config entry.
     */
    private function removeFormConfig(string $slug): void
    {
        $configPath = config_path('service_forms.php');

        if (! file_exists($configPath)) {
            return;
        }

        $config = include $configPath;

        unset($config[$slug]);

        $content = "<?php\n\nreturn ".$this->arrayToPhp($config).";\n";

        file_put_contents($configPath, $content);
    }

    /**
     * Convert a PHP array to a formatted PHP string representation.
     */
    private function arrayToPhp(array $array, int $indent = 1): string
    {
        $pad = str_repeat('    ', $indent);
        $closePad = str_repeat('    ', $indent - 1);
        $lines = [];

        $isSequential = array_is_list($array);

        foreach ($array as $key => $value) {
            $keyStr = $isSequential ? '' : "'".addslashes((string) $key)."' => ";

            if (is_array($value)) {
                $lines[] = $pad.$keyStr.$this->arrayToPhp($value, $indent + 1).',';
            } elseif (is_bool($value)) {
                $lines[] = $pad.$keyStr.($value ? 'true' : 'false').',';
            } elseif (is_null($value)) {
                $lines[] = $pad.$keyStr.'null,';
            } elseif (is_numeric($value) && ! is_string($value)) {
                $lines[] = $pad.$keyStr.$value.',';
            } else {
                $lines[] = $pad.$keyStr."'".addslashes((string) $value)."',";
            }
        }

        return "[\n".implode("\n", $lines)."\n".$closePad.']';
    }
}
