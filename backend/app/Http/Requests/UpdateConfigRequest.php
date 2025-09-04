<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20|regex:/^(\+?\d{1,3}[- ]?)?\d{10,15}$/',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'facebook' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'main_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',

            'logo_header' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_footer' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.max' => 'Tiêu đề không được vượt quá :max ký tự.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá :max ký tự.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được vượt quá :max ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá :max ký tự.',
            'facebook.url' => 'URL Facebook không hợp lệ.',
            'youtube.url' => 'URL YouTube không hợp lệ.',
            'tiktok.url' => 'URL TikTok không hợp lệ.',
            'twitter.url' => 'URL Twitter không hợp lệ.',
            'instagram.url' => 'URL Instagram không hợp lệ.',
            'main_color.regex' => 'Màu chính phải là mã màu hex hợp lệ (ví dụ: #FFFFFF).',
            'logo_header.image' => 'Logo header phải là một hình ảnh.',
            'logo_header.mimes' => 'Logo header phải có định dạng jpeg, png, jpg hoặc gif.',
            'logo_header.max' => 'Kích thước logo header không được vượt quá :max KB.',
            'logo_footer.image' => 'Logo footer phải là một hình ảnh.',
            'logo_footer.mimes' => 'Logo footer phải có định dạng jpeg, png, jpg hoặc gif.',
            'logo_footer.max' => 'Kích thước logo footer không được vượt quá :max KB.',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'phone' => 'Số điện thoại',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'facebook' => 'Facebook',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'twitter' => 'Twitter',
            'instagram' => 'Instagram',
            'main_color' => 'Màu chính',
            'logo_header' => 'Logo header',
            'logo_footer' => 'Logo footer',
        ];
    }
}
