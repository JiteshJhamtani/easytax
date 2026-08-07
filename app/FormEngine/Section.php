<?php

namespace App\FormEngine;

class Section
{
    public string $label;
    public array $fields = [];

    public function __construct(array $config)
    {
        $this->label = $config['label'];

        foreach ($config['fields'] as $fieldConfig) {
            $this->fields[] = new Field($fieldConfig);
        }
    }

    public function render(): string
    {
        return view('components.form.section', [
            'section' => $this
        ])->render();
    }

    public function rules(): array
    {
        $rules = [];

        foreach ($this->fields as $field) {
            if ($field->rule()) {
                $rules[$field->name] = $field->rule();
            }
        }

        return $rules;
    }
}
