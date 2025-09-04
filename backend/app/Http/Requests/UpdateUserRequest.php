<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('tbl_users', 'email')->ignore($this->user()->id),
            ],
            'phone' => [
                'nullable',
                'regex:/^(0|\+84)[0-9]{9}$/',
                Rule::unique('tbl_users', 'phone')->ignore($this->user()->id),
            ],
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => ':attribute không được để trống.',
            'email.regex' => ':attribute không đúng định dạng.',
            'email.unique' => ':attribute đã tồn tại.',

            'phone.regex' => ':attribute không đúng định dạng (phải là số Việt Nam).',
            'phone.unique' => ':attribute đã được sử dụng.',

            'avatar.image' => ':attribute phải là hình ảnh.',
            'avatar.max' => ':attribute không được vượt quá :max KB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'avatar' => 'Ảnh đại diện',
        ];
    }
}
