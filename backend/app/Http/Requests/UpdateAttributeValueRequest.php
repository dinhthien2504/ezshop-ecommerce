<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttributeValueRequest extends FormRequest
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
            'value' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_attribute_values')->where(function ($query) {
                    // Thêm điều kiện phân biệt hoa thường và kiểm tra theo attribute_id
                    return $query->where('attribute_id', $this->attribute_id)
                        ->whereRaw('BINARY value = ?', [$this->value]);
                })
                    ->ignore($this->route('id')) // Bỏ qua bản ghi hiện tại
            ],
        ];
    }

    public function messages()
    {
        return [
            'value.required' => ':attribute không được để trống',
            'value.max' => ':attribute không được vượt quá :max ký tự.',
            'value.unique' => ':attribute đã tồn tại.',
        ];
    }

    public function attributes()
    {
        return [
            'value' => 'Giá trị',
        ];
    }
}
