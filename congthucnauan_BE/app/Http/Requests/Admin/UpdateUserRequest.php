<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->route('id'))],
            'role' => 'required|in:user,manager,admin'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Họ tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.unique' => 'Email đã được sử dụng',
            'role.required' => 'Vai trò là bắt buộc',
            'role.in' => 'Vai trò không hợp lệ'
        ];
    }
}
