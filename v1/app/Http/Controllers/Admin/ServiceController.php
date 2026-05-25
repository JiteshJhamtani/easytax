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
            ->withCount('applications');

        return DataTables::of($services)

            ->addColumn('commission_display', function ($service) {
                if ($service->commission_type === 'percentage') {
                    return $service->commission_value.'%';
                }

                return '₹'.number_format($service->commission_value, 2);
            })

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
            'total_applications' => $service->applications()->count(),
            'total_revenue' => $service->applications()->sum('amount'),
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

        $formSchema = $this->parseFormSchema($data);
        unset($data['form_schema']);

        $oldSlug = $service->slug;

        $service->update($data);

        if ($formSchema) {
            // If slug changed, remove old config key
            if ($oldSlug !== $service->slug) {
                $this->removeFormConfig($oldSlug);
            }

            $this->writeFormConfig($service->slug, $formSchema);
        }

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
