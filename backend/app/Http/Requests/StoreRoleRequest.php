<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'title' => 'required|string|max:255|unique:tbl_roles,title',
            'description' => 'nullable|string|max:1000',
            'role_status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tên vai trò không được để trống.',
            'title.string' => 'Tên vai trò phải là chuỗi.',
            'title.max' => 'Tên vai trò không được vượt quá 255 ký tự.',
            'title.unique' => 'Tên vai trò đã tồn tại.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'role_status.boolean' => 'Trạng thái không hợp lệ.',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Vai trò',
            'description' => 'Mô tả',
            'role_status' => 'Trạng thái'
        ];
    }
}
