<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
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
            'title'     => 'required|string|max:255',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link'      => 'nullable|url',
            'position'  => 'nullable|in:home_1,home_2,home_3,,mobile_1,mobile_2,mobile_3,slider',
            'priority'  => 'nullable|integer|min:0|max:10',
            'start_at'  => 'nullable|date',
            'end_at'    => 'nullable|date|after_or_equal:start_at',
        ];
    }

    public function messages()
    {
        return [
            'title.max' => 'Tiêu đề không được vượt quá :max ký tự.',
            'image.required' => 'Hình ảnh là bắt buộc.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif hoặc webp.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá :max KB.',
            'link.url' => 'Liên kết phải là một URL hợp lệ.',
            'position.in' => 'Vị trí phải là một trong các giá trị: home_1, home_2, home_3, slider.',
            'priority.integer' => 'Ưu tiên phải là một số nguyên.',
            'priority.min' => 'Ưu tiên không được nhỏ hơn 0.',
            'priority.max' => 'Ưu tiên không được lớn hơn 10.',
            'start_at.date' => 'Ngày bắt đầu phải là một ngày hợp lệ.',
            'end_at.date' => 'Ngày kết thúc phải là một ngày hợp lệ.',
            'end_at.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Tiêu đề',
            'image' => 'Hình ảnh',
            'link' => 'Liên kết',
            'position' => 'Vị trí',
            'priority' => 'Ưu tiên',
            'start_at' => 'Ngày bắt đầu',
            'end_at' => 'Ngày kết thúc',
        ];
    }
}
