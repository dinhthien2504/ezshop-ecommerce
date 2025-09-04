<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_name' => 'required|string|min:6|max:50|unique:tbl_users,user_name',
            'email' => [
                'required',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:tbl_users,email',
            ],
            'password' => [
                'required',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
            'phone' => 'nullable|max:15',
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {   
        return [
            'user_name.required' => ':attribute không được để trống.',
            'user_name.max' => ':attribute không được vượt quá :max ký tự.',
            'user_name.unique' => ':attribute đã tồn tại.',
            'email.required' => ':attribute không được để trống.',
            'email.regex' => ':attribute không đúng định dạng.',
            'email.unique' => ':attribute đã tồn tại.',
            'password.required' => ':attribute không được để trống.',
            'password.min' => ':attribute phải có ít nhất :min ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.regex' => ':attribute phải chứa ít nhất 1 ký tự đặt biệt, 1 chữ hoa và 1 chữ số.',
            'phone.max' => ':attribute không được vượt quá :max ký tự.',
            'avatar.image' => ':attribute phải là một hình ảnh.',
            'avatar.max' => ':attribute không được vượt quá :max KB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_name' => 'Tên người dùng',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoại',
            'avatar' => 'Hình ảnh đại diện',
        ];
    }
}
