<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'driver' => 'required|string',
            'key' => 'nullable|string',
            'host' => 'nullable|string',
            'config' => 'nullable|array',
            'active' => 'nullable|boolean',
        ];
    }
}
