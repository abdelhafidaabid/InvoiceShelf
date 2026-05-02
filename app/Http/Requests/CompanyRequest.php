<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('companies')->ignore($this->header('company'), 'id'),
            ],
            'vat_id' => [
                'nullable',
            ],
            'tax_id' => [
                'nullable',
            ],
            'pdf_main_color' => [
                'nullable',
            ],
            'pdf_secondary_color' => [
                'nullable',
            ],
            'patente' => [
                'nullable',
            ],
            'cnss' => [
                'nullable',
            ],
            'rc' => [
                'nullable',
            ],
            'ice' => [
                'nullable',
            ],
            'slug' => [
                'nullable',
            ],
            'address.country_id' => [
                'required',
            ],
            'address.email' => [
                'nullable',
                'email',
            ],
            'address.website' => [
                'nullable',
            ],
            'address.fax' => [
                'nullable',
            ],
        ];
    }

    public function getCompanyPayload()
    {
        return collect($this->validated())
            ->only([
                'name',
                'slug',
                'vat_id',
                'tax_id',
                'pdf_main_color',
                'pdf_secondary_color',
                'patente',
                'cnss',
                'rc',
                'ice',
            ])
            ->toArray();
    }
}
