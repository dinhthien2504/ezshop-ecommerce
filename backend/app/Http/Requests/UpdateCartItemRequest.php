<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'variant_id' => ['sometimes', 'exists:tbl_product_variants,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'variant_id.exists'  => 'Biến thể không tồn tại.',
            'quantity.required'  => 'Vui lòng nhập số lượng.',
            'quantity.integer'   => 'Số lượng phải là số nguyên.',
            'quantity.min'       => 'Số lượng tối thiểu là 1.',
        ];
    }
}
