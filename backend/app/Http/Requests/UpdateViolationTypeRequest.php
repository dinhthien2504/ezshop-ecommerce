<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateViolationTypeRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max: 100',
                Rule::unique('tbl_violation_types', 'name')->ignore($this->route('id')),
            ],
            'code' => [
                'required',
                'string',
                'max: 20',
                Rule::unique('tbl_violation_types', 'code')->ignore($this->route('id')),
            ],
        ];
    }

    /**
     * Get the custom messages for the validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên loại vi phạm là bắt buộc.',
            'name.max' => 'Tên loại vi phạm không được vượt quá 100 ký tự.',
            'name.unique' => 'Tên loại vi phạm đã tồn tại.',
            'code.required' => 'Mã loại vi phạm là bắt buộc.',
            'code.max' => 'Mã loại vi phạm không được vượt quá 20 ký tự.',
            'code.unique' => 'Mã loại vi phạm đã tồn tại.',
        ];
    }

    /**
     * Get the custom attribute names for the validation rules.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Tên loại vi phạm',
            'code' => 'Mã loại vi phạm',
        ];
    }
}
