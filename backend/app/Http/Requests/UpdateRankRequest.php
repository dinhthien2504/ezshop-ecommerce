<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRankRequest extends FormRequest
{
    /**
     * Xác định người dùng có quyền gửi request không.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quy tắc validate.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_ranks', 'name')->ignore($this->route('id')),
            ],
            'min_spent' => 'required|integer|min:0',
            'max_spent' => 'required|integer|min:0|gte:min_spent',
            'benefits' => 'nullable|string',
        ];
    }

    /**
     * Thông báo lỗi.
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Tên cấp bậc là bắt buộc.',
            'name.max'           => 'Tên cấp bậc không được vượt quá 50 ký tự.',
            'name.unique'        => 'Tên cấp bậc đã tồn tại.',
            'min_spent.required' => 'Giá trị tối thiểu là bắt buộc.',
            'min_spent.integer'  => 'Giá trị tối thiểu phải là số nguyên.',
            'min_spent.min'      => 'Giá trị tối thiểu không được âm.',
            'max_spent.required' => 'Giá trị tối đa là bắt buộc.',
            'max_spent.integer'  => 'Giá trị tối đa phải là số nguyên.',
            'max_spent.min'      => 'Giá trị tối đa không được âm.',
            'max_spent.gte'      => 'Giá trị tối đa phải lớn hơn hoặc bằng giá trị tối thiểu.',
        ];
    }

    /**
     * Gán tên tiếng Việt cho các trường.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Cấp bậc',
            'min_spent' => 'Chi tiêu tối thiểu',
            'max_spent' => 'Chi tiêu tối đa',
            'benefits' => 'Lợi ích'
        ];
    }
}
