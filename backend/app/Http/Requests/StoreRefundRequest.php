<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id'    => 'required|exists:tbl_orders,id',
            'address_id'  => 'required|exists:tbl_address,id',
            'total_refund'=> 'required|numeric|min:0',
            'status'      => 'nullable|in:pending,approved,rejected,processed',
        ];
    }

    public function messages()
    {
        return [
            'order_id.required'    => 'Vui lòng chọn đơn hàng cần hoàn tiền.',
            'order_id.exists'      => 'Đơn hàng không tồn tại.',
            'user_id.required'     => 'Vui lòng chọn người yêu cầu hoàn tiền.',
            'user_id.exists'       => 'Người dùng không tồn tại.',
            'address_id.required'  => 'Vui lòng chọn địa chỉ hoàn tiền.',
            'address_id.exists'    => 'Địa chỉ không tồn tại.',
            'total_refund.required'=> 'Vui lòng nhập số tiền hoàn.',
            'total_refund.numeric' => 'Số tiền hoàn phải là số.',
            'total_refund.min'     => 'Số tiền hoàn phải lớn hơn hoặc bằng 0.',
            'status.in'            => 'Trạng thái không hợp lệ.',
        ];
    }
}