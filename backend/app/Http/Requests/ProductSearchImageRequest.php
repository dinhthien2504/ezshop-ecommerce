<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120'
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Vui lòng chọn một hình ảnh.',
            'image.image' => 'Tập tin phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif, svg, hoặc webp.',
            'image.max' => 'Ảnh không được vượt quá 5MB.',
        ];
    }
}
