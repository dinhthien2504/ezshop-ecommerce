<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:100|unique:tbl_categories,name,' . $this->route('id'),
            'tax_id' => 'required',
            'fee_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => ':attribute danh mục không được để trống',
            'name.max' => ':attribute không được vượt quá :max ký tự.',
            'name.unique' => ':attribute danh mục đã tồn tại.',
            'tax_id.required' => 'Vui lòng chọn :attribute.',
            'fee_id.required' => 'Vui lòng chọn :attribute.'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên',
            'tex_id' => 'thuế',
            'fee_id' => 'phí',
        ];
    }
}
