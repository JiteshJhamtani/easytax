<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormValidator
{
    public function validate(string $serviceSlug, array $input): array
    {
        $serviceConfig = config("service_forms.$serviceSlug");

        if (!$serviceConfig) {
            throw ValidationException::withMessages([
                'service' => 'Invalid service selected.',
            ]);
        }

        $rules    = [];
        $messages = [];

        foreach ($serviceConfig['sections'] as $section) {
            foreach ($section['fields'] as $field) {

                $fieldName = $field['name'];

                if (!empty($field['validation'])) {
                    $rules[$fieldName] = $field['validation'];
                }

                if (!empty($field['required']) && $field['required']) {
                    $messages["{$fieldName}.required"] = "{$field['label']} is required.";
                }
            }
        }

        return Validator::make($input, $rules, $messages)->validate();
    }
}
