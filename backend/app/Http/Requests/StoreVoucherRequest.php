<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:100',
            'code' => 'required|string|max:255|unique:tbl_vouchers,code,' . $this->id,
            'type' => 'required|in:fixed_amount,percentage_discount,free_shipping',
            'source' => 'nullable|in:platform,shop',
            'quantity' => 'required|integer|min:1',
            'limit' => 'required|integer|min:1',
            'min' => 'required|numeric|min:0',
            'max' => 'required|numeric|gt:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ];

        if ($this->type === 'percentage_discount') {
            $rules['percent'] = 'required|integer|min:1|max:100';
        } elseif ($this->type === 'free_shipping' && $this->has('percent')) {
            $rules['percent'] = 'nullable|integer|min:1|max:100';
        } else {
            $rules['percent'] = 'nullable';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'code.required' => 'Mã giảm giá không được để trống.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',
            'type.required' => 'Vui lòng chọn loại ưu đãi.',
            'quantity.required' => 'Số lượng phải lớn hơn 0.',
            'limit.required' => 'Giới hạn dùng phải lớn hơn 0.',
            'min.required' => 'Đơn tối thiểu không hợp lệ.',
            'max.required' => 'Giảm tối đa phải lớn hơn 0.',
            'percent.min' => 'Phần trăm phải từ 1 đến 100.',
            'percent.max' => 'Phần trăm phải từ 1 đến 100.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ];
    }
}
