<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeRequest extends FormRequest
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
            'name' => 'required|string|unique:tbl_fees,name',
            'percentage' => 'required|numeric|between:0,100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute Phí không được để trống.',
            'name.unique' => ':attribute Phí đã tồn tại.',
            'percentage.required' => 'Phí không được để trống.',
            'percentage.between' => 'Phí phải từ :min đến :max.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên'
        ];
    }
}
