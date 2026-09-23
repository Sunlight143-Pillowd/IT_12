<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePcBuildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
