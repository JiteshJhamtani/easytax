<?php

namespace App\FormEngine;

use App\Models\Service;

class Form
{
    protected Service $service;
    protected array $config;
    protected array $sections = [];

    public static function fromService(Service $service): self
    {
        return new self($service);
    }

    public function __construct(Service $service)
    {
        $this->service = $service;

        $this->config = config("service_forms.{$service->slug}");

        if (!$this->config || !isset($this->config['sections'])) {
            abort(404);
        }

        foreach ($this->config['sections'] as $sectionConfig) {
            $this->sections[] = new Section($sectionConfig);
        }

        if (isset($this->config['documents'])) {
            $documentFields = [];
            foreach ($this->config['documents'] as $docConfig) {
                $validation = $docConfig['required'] ? 'required|file' : 'nullable|file';
                if (isset($docConfig['mimes'])) {
                    $validation .= '|mimes:' . implode(',', $docConfig['mimes']);
                }

                $documentFields[] = [
                    'name'       => 'documents[' . $docConfig['name'] . ']',
                    'label'      => $docConfig['label'],
                    'type'       => 'file',
                    'required'   => $docConfig['required'],
                    'validation' => $validation,
                ];
            }

            $this->sections[] = new Section([
                'label'  => 'Required Documents',
                'fields' => $documentFields,
            ]);
        }
    }

    public function render(): string
    {
        return view('components.form.wrapper', [
            'form' => $this
        ])->render();
    }

    public function sections(): array
    {
        return $this->sections;
    }

    public function rules(): array
    {
        $rules = [];

        foreach ($this->sections as $section) {
            $rules = array_merge($rules, $section->rules());
        }

        return $rules;
    }

    public function service(): Service
    {
        return $this->service;
    }
}
