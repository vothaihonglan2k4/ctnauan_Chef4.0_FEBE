<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class AdminCommentController extends Controller
{
    public function index(Request $request)
    {
        $query = ForumComment::with(['user', 'post'])
            ->select('forum_comments.*');

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('post', function($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by post
        if ($request->has('post_id') && $request->post_id !== '') {
            $query->where('post_id', $request->post_id);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        // Sort
        $query->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 10);
        $comments = $query->paginate($perPage);

        return response()->json([
            'comments' => $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $comment->user->name,
                    'post_title' => $comment->post->title,
                    'post_id' => $comment->post_id,
                    'parent_id' => $comment->parent_id,
                    'created_at' => $comment->created_at
                ];
            }),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total()
            ]
        ]);
    }

    public function show($id)
    {
        $comment = ForumComment::with(['user', 'post'])->findOrFail($id);

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar
                ],
                'post' => [
                    'id' => $comment->post->id,
                    'title' => $comment->post->title
                ],
                'parent_id' => $comment->parent_id,
                'created_at' => $comment->created_at
            ]
        ]);
    }

    public function destroy($id)
    {
        $comment = ForumComment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Xóa bình luận thành công'
        ]);
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'message' => 'Vui lòng chọn ít nhất một bình luận'
            ], 400);
        }

        ForumComment::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => 'Xóa ' . count($ids) . ' bình luận thành công'
        ]);
    }
}
