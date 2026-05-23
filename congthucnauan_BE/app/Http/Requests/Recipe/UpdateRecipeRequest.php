<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Recipe;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $recipe = Recipe::find($this->route('id'));
        if (!$recipe) return false;
        return $recipe->user_id === $this->user()->id || $this->user()->isAdmin();
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
            'description.required' => 'Mô tả là bắt buộc',
            'ingredients.required' => 'Nguyên liệu là bắt buộc',
            'instructions.required' => 'Hướng dẫn là bắt buộc',
            'category_id.required' => 'Danh mục là bắt buộc',
            'category_id.exists' => 'Danh mục không tồn tại'
        ];
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('Bạn không có quyền chỉnh sửa công thức này');
    }
}
