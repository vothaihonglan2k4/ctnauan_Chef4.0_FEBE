<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'active' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên phòng học là bắt buộc',
            'capacity.required' => 'Sức chứa là bắt buộc',
            'capacity.min' => 'Sức chứa phải lớn hơn 0',
            'location.required' => 'Vị trí là bắt buộc'
        ];
    }
}
