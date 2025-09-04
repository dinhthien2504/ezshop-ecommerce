<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            // 'product_name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            // 'price' => 'required|numeric|min:0',
            // 'stock' => 'required|integer|min:0',
            // 'category_id' => 'required|exists:tbl_categories,id',
            // 'images' => 'nullable|array',
            // 'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // 'variants' => 'nullable|array',
            // 'variants.*.attributes' => 'required|array',
            // 'variants.*.attributes.*' => 'string|max:100',
            // 'variants.*.price' => 'required|numeric|min:0',
            // 'variants.*.stock' => 'required|integer|min:0',
            // 'variants.*.image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            // 'product_name.required' => 'Tên sản phẩm là bắt buộc.',
            // 'product_name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            // 'product_name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            // 'description.string' => 'Mô tả phải là chuỗi ký tự.',

            // 'price.required' => 'Giá sản phẩm là bắt buộc.',
            // 'price.numeric' => 'Giá sản phẩm phải là số.',
            // 'price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0.',

            // 'stock.required' => 'Số lượng tồn kho là bắt buộc.',
            // 'stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            // 'stock.min' => 'Số lượng tồn kho không được âm.',

            // 'category_id.required' => 'Danh mục là bắt buộc.',
            // 'category_id.exists' => 'Danh mục không tồn tại.',

            // 'images.array' => 'Danh sách ảnh phải là một mảng.',
            // 'images.*.image' => 'Mỗi tệp phải là một ảnh.',
            // 'images.*.mimes' => 'Ảnh chỉ chấp nhận định dạng: jpeg, png, jpg, gif, webp.',
            // 'images.*.max' => 'Kích thước ảnh không được vượt quá 2MB.',

            // 'variants.array' => 'Biến thể phải là một mảng.',
            // 'variants.*.attributes.required' => 'Biến thể phải có thuộc tính.',
            // 'variants.*.attributes.array' => 'Thuộc tính biến thể phải là mảng.',
            // 'variants.*.attributes.*.string' => 'Mỗi thuộc tính trong biến thể phải là chuỗi.',
            // 'variants.*.attributes.*.max' => 'Thuộc tính không được vượt quá 100 ký tự.',

            // 'variants.*.price.required' => 'Giá biến thể là bắt buộc.',
            // 'variants.*.price.numeric' => 'Giá biến thể phải là số.',
            // 'variants.*.price.min' => 'Giá biến thể phải lớn hơn hoặc bằng 0.',

            // 'variants.*.stock.required' => 'Tồn kho biến thể là bắt buộc.',
            // 'variants.*.stock.integer' => 'Tồn kho biến thể phải là số nguyên.',
            // 'variants.*.stock.min' => 'Tồn kho biến thể không được âm.',

            // 'variants.*.image.image' => 'Ảnh biến thể phải là ảnh.',
            // 'variants.*.image.mimes' => 'Ảnh biến thể chỉ chấp nhận jpeg, png, jpg, gif, webp.',
            // 'variants.*.image.max' => 'Ảnh biến thể không được vượt quá 2MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            // 'product_name' => 'tên sản phẩm',
            // 'description' => 'mô tả',
            // 'price' => 'giá sản phẩm',
            // 'stock' => 'số lượng tồn kho',
            // 'category_id' => 'danh mục',
            // 'images' => 'ảnh sản phẩm',
            // 'images.*' => 'ảnh trong danh sách',

            // 'variants' => 'biến thể',
            // 'variants.*.attributes' => 'thuộc tính của biến thể',
            // 'variants.*.attributes.*' => 'thuộc tính',
            // 'variants.*.price' => 'giá biến thể',
            // 'variants.*.stock' => 'tồn kho biến thể',
            // 'variants.*.image' => 'ảnh biến thể',
        ];
    }

}
