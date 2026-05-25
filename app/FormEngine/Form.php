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

        if (! $this->config || ! isset($this->config['sections'])) {
            abort(404);
        }

        $mergedSections = [];
        $baseSectionsMap = [
            'director' => 'director_details',
            'partner' => 'partner_details',
            'contact' => 'contact_details',
            'member' => 'member_details',
        ];

        foreach ($this->config['sections'] as $key => $sectionConfig) {
            $isNumberedSection = false;

            // Check if this is a numbered section like director_1_details
            foreach (['director', 'partner', 'contact', 'member'] as $type) {
                if (preg_match('/^'.$type.'_\d+_details$/', $key)) {
                    $isNumberedSection = true;
                    $baseKey = $baseSectionsMap[$type];

                    // If the base section exists, merge fields into it
                    if (isset($mergedSections[$baseKey])) {
                        $mergedSections[$baseKey]['fields'] = array_merge(
                            $mergedSections[$baseKey]['fields'],
                            $sectionConfig['fields']
                        );
                    } else {
                        // Fallback if base section is missing
                        $mergedSections[$key] = $sectionConfig;
                    }
                    break;
                }
            }

            if (! $isNumberedSection) {
                $mergedSections[$key] = $sectionConfig;
            }
        }

        foreach ($mergedSections as $sectionConfig) {
            $this->sections[] = new Section($sectionConfig);
        }

        if (isset($this->config['documents'])) {
            $documentFields = [];
            foreach ($this->config['documents'] as $docConfig) {
                $validation = $docConfig['required'] ? 'required|file' : 'nullable|file';
                if (isset($docConfig['mimes'])) {
                    $validation .= '|mimes:'.implode(',', $docConfig['mimes']);
                }

                $documentFields[] = [
                    'name' => 'documents['.$docConfig['name'].']',
                    'label' => $docConfig['label'],
                    'type' => 'file',
                    'required' => $docConfig['required'],
                    'validation' => $validation,
                ];
            }

            $this->sections[] = new Section([
                'label' => 'Required Documents',
                'fields' => $documentFields,
            ]);
        }
    }

    public function render(): string
    {
        return view('components.form.wrapper', [
            'form' => $this,
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
