<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'commission_type' => 'required|string|in:flat,percentage',
            'commission_value' => 'required|numeric|min:0',
            'form_schema' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'price.min' => 'Price cannot be negative.',
        ];
    }
}
