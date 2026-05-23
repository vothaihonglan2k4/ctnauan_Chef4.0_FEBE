<?php

namespace App\Http\Requests\Rating;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Đánh giá là bắt buộc',
            'rating.integer' => 'Đánh giá phải là số nguyên',
            'rating.min' => 'Đánh giá tối thiểu là 1 sao',
            'rating.max' => 'Đánh giá tối đa là 5 sao',
            'comment.max' => 'Bình luận không được vượt quá 500 ký tự'
        ];
    }
}
