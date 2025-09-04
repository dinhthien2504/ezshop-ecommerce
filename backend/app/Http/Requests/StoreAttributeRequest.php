<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttributeRequest extends FormRequest
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
                'max:50',
                Rule::unique('tbl_attributes')->where(function ($query) {
                    // Thêm điều kiện phân biệt hoa thường
                    return $query->whereRaw('BINARY name = ?', [$this->name]);
                })
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => ':attribute thuộc tính không được để trống',
            'name.max' => ':attribute không được vượt quá :max ký tự.',
            'name.unique' => ':attribute thuộc tính đã tồn tại.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên',
        ];
    }
}
