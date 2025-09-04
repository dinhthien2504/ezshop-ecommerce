<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'login' => 'required|string',
            'password' => 'required|string|min:8'
        ];
    }

    public function messages()
    {
        return [
            'login.required' => ':attribute không được để trống',
            'password.required' => ':attribute không được để trống',
            'login.string' => ':attribute Vui lòng nhập tài khoản hợp lệ.',
            'password.string' => 'Vui lòng nhập :attribute hợp lệ.',
            'password.min' => ':attribute phải có ít nhất 8 kí tự.',
        ];
    }

    public function attributes()
    {
        return [
            'login' => 'Tài khoản',
            'password'=> 'Mật khẩu'
        ];
    }
}
