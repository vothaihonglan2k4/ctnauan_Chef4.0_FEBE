<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'classroom_id' => 'required|exists:classrooms,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer',
            'status' => 'in:draft,published,archived',
            'image' => 'nullable|image|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tên khóa học là bắt buộc',
            'classroom_id.required' => 'Phòng học là bắt buộc',
            'classroom_id.exists' => 'Phòng học không tồn tại',
            'price.required' => 'Giá là bắt buộc',
            'price.min' => 'Giá không được âm'
        ];
    }
}
