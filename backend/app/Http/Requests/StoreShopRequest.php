<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_name' => 'required|string|min:3',
            'name' => 'required|string|min:5',
            'detail_address' => 'required|string|min:5',
            'ward_code' => 'required'
        ];
    }

    public function messages()
    {
        return [

            'shop_name.required' => ':attribute không được để trống.',
            'shop_name.string' => ':attribute phải là chuỗi ký tự.',
            'shop_name.min' => ':attribute phải có ít nhất :min ký tự.',

            'name.required' => ':attribute không được để trống.',
            'name.string' => ':attribute phải là chuỗi ký tự.',
            'name.min' => ':attribute phải có ít nhất :min ký tự.',

            'detail_address.required' => ':attribute không được để trống.',
            'detail_address.string' => ':attribute phải là chuỗi ký tự.',
            'detail_address.min' => ':attribute phải có ít nhất :min ký tự.',

            'ward_id.required' => ':attribute không được để trống.',
        ];
    }

    public function attributes()
    {
        return [
            'shop_name' => 'Tên cửa hàng',
            'name' => 'Tên người đại diện',
            'detail_address' => 'Địa chỉ chi tiết',
            'ward_id' => 'Phường/Xã',
        ];
    }
}
