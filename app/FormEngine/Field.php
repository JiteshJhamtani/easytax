<?php

namespace App\FormEngine;

use Exception;

class Field
{
    protected static array $allowedTypes = [
        'text',
        'email',
        'number',
        'date',
        'password',
        'textarea',
        'select',
        'file',
    ];

    public string $name;
    public string $label;
    public string $type;
    public bool $required;
    public ?string $validation;
    public array $options;

    public function __construct(array $config)
    {
        if (!in_array($config['type'], self::$allowedTypes)) {
            throw new Exception("Unsupported field type: {$config['type']}");
        }

        $this->name       = $config['name'];
        $this->label      = $config['label'];
        $this->type       = $config['type'];
        $this->required   = $config['required'] ?? false;
        $this->validation = $config['validation'] ?? null;
        $this->options    = $config['options'] ?? [];
    }

    public function render(): string
    {
        return view("components.form.fields.{$this->type}", [
            'field' => $this
        ])->render();
    }

    public function rule(): ?string
    {
        return $this->validation;
    }
}
