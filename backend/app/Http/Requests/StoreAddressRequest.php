<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'phone' => 'required|string',
            'address_detail' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên người gửi!',
            'name.max' => 'Tên tối đã :max ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại!',
            'address_detail.required' => 'Vui lòng nhập địa chỉ chi tiết!',
        ];
    }

}
