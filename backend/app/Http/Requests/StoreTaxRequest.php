<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:tbl_taxes,name',
            'vat_percent' => 'required|numeric|between:0,100',
            'tax_percent' => 'required|numeric|between:0,100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute thuế không được để trống.',
            'name.unique' => ':attribute thuế đã tồn tại.',
            'vat_percent.required' => 'Thuế VAT không được để trống.',
            'vat_percent.between' => 'Thuế VAT phải từ :min đến :max.',
            'tax_percent.required' => 'Thuế TAX không được để trống.',
            'tax_percent.between' => 'Thuế TAX phải từ :min đến :max.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên'
        ];
    }
}
