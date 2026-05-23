<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ForumPost;

class UpdateForumPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = ForumPost::find($this->route('id'));
        
        if (!$post) {
            return false;
        }
        
        return $post->user_id === $this->user()->id || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:5120', // 5MB
            'video_url' => 'nullable|url',
            'recipe_id' => 'nullable|exists:recipes,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề bài viết là bắt buộc',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'content.required' => 'Nội dung bài viết là bắt buộc',
            'image.image' => 'File phải là hình ảnh',
            'image.max' => 'Hình ảnh không được vượt quá 5MB',
            'video_url.url' => 'URL video không hợp lệ',
            'recipe_id.exists' => 'Công thức không tồn tại',
            'tags.array' => 'Tags phải là một mảng',
            'tags.*.max' => 'Mỗi tag không được vượt quá 50 ký tự'
        ];
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('Bạn không có quyền chỉnh sửa bài viết này');
    }
}
