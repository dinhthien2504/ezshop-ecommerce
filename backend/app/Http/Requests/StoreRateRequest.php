<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'feedbacks' => 'required|array',
            'feedbacks.*.order_detail_id' => 'required|integer',
            'feedbacks.*.review' => 'nullable|string',
            'feedbacks.*.images' => 'array|max:5',
        ];
    }
}