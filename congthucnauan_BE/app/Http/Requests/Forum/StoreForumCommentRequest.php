<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_comments,id'
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Nội dung bình luận là bắt buộc',
            'parent_id.exists' => 'Bình luận cha không tồn tại'
        ];
    }
}
