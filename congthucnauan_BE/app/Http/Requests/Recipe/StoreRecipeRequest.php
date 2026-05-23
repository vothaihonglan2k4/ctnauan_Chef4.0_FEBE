<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'cooking_time' => 'nullable|integer',
            'servings' => 'nullable|integer',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'image' => 'nullable|image|max:5120',
            'video_url' => 'nullable|url'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tên công thức là bắt buộc',
            'title.max' => 'Tên công thức không được vượt quá 200 ký tự',
            'description.required' => 'Mô tả là bắt buộc',
            'ingredients.required' => 'Nguyên liệu là bắt buộc',
            'instructions.required' => 'Hướng dẫn là bắt buộc',
            'category_id.required' => 'Danh mục là bắt buộc',
            'category_id.exists' => 'Danh mục không tồn tại',
            'image.image' => 'File phải là hình ảnh',
            'image.max' => 'Hình ảnh không được vượt quá 5MB',
            'video_url.url' => 'URL video không hợp lệ'
        ];
    }
}
