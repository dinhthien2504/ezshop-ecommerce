<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'bank_short_name' => 'nullable|string|max:100',
            'bank_logo' => 'nullable|url',
            'bin_account' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'bank_account_name.required' => 'Vui lòng nhập tên tài khoản.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản.',
            'bank_name.required' => 'Vui lòng chọn ngân hàng.',
            'bin_account.required' => 'Thiếu mã BIN của ngân hàng.',
        ];
    }
}
