<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreForumPostRequest;
use App\Http\Requests\Forum\UpdateForumPostRequest;
use App\Http\Requests\Forum\StoreForumCommentRequest;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumLike;
use App\Models\ForumTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /**
     * Get all forum posts
     */
    public function index(Request $request)
    {
        $query = ForumPost::with(['user', 'tags'])
            ->withCount(['comments', 'likes']);

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sort - pinned posts first
        $query->orderBy('is_pinned', 'desc')
              ->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 15);
        $posts = $query->paginate($perPage);

        return response()->json([
            'posts' => $posts->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => Str::limit($post->content, 200),
                    'views' => $post->views,
                    'is_pinned' => $post->is_pinned,
                    'comments_count' => $post->comments_count,
                    'likes_count' => $post->likes_count,
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                        'avatar' => $post->user->avatar
                    ],
                    'tags' => $post->tags->pluck('name'),
                    'created_at' => $post->created_at
                ];
            }),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total()
            ]
        ]);
    }

    /**
     * Get single post
     */
    public function show($id)
    {
        $post = ForumPost::with(['user', 'tags', 'comments' => function($query) {
            $query->whereNull('parent_id')
                  ->with(['user', 'replies.user'])
                  ->orderBy('created_at', 'desc');
        }])
        ->withCount(['comments', 'likes'])
        ->findOrFail($id);

        // Increment views
        $post->increment('views');

        $user = auth('sanctum')->user();
        $hasLiked = false;
        
        if ($user) {
            $hasLiked = ForumLike::where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        return response()->json([
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'views' => $post->views,
                'is_pinned' => $post->is_pinned,
                'image' => $post->image,
                'video_url' => $post->video_url,
                'recipe_id' => $post->recipe_id,
                'user' => [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                    'avatar' => $post->user->avatar
                ],
                'tags' => $post->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name
                    ];
                }),
                'comments_count' => $post->comments_count,
                'likes_count' => $post->likes_count,
                'has_liked' => $hasLiked,
                'comments' => $post->comments->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'avatar' => $comment->user->avatar
                        ],
                        'replies' => $comment->replies->map(function($reply) {
                            return [
                                'id' => $reply->id,
                                'content' => $reply->content,
                                'user' => [
                                    'id' => $reply->user->id,
                                    'name' => $reply->user->name,
                                    'avatar' => $reply->user->avatar
                                ],
                                'created_at' => $reply->created_at
                            ];
                        }),
                        'created_at' => $comment->created_at
                    ];
                }),
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ]
        ]);
    }

    /**
     * Create new post
     */
    public function store(StoreForumPostRequest $request)
    {
        try {
            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => $request->user()->id,
                'views' => 0,
                'is_pinned' => false,
                'video_url' => $request->video_url,
                'recipe_id' => $request->recipe_id,
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/forum'), $filename);
                $data['image'] = $filename;
            }

            $post = ForumPost::create($data);

            // Add tags
            if ($request->has('tags')) {
                foreach ($request->tags as $tagName) {
                    $tag = ForumTag::firstOrCreate(['name' => $tagName]);
                    $post->tags()->attach($tag->id);
                }
            }

            return response()->json([
                'message' => 'Bài viết đã được tạo',
                'post' => $post->load('tags')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating post',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Update post
     */
    public function update(UpdateForumPostRequest $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'video_url' => $request->video_url,
            'recipe_id' => $request->recipe_id,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image && file_exists(public_path('uploads/forum/' . $post->image))) {
                unlink(public_path('uploads/forum/' . $post->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/forum'), $filename);
            $data['image'] = $filename;
        }

        $post->update($data);

        // Update tags
        if ($request->has('tags')) {
            $post->tags()->detach();
            foreach ($request->tags as $tagName) {
                $tag = ForumTag::firstOrCreate(['name' => $tagName]);
                $post->tags()->attach($tag->id);
            }
        }

        return response()->json([
            'message' => 'Bài viết đã được cập nhật',
            'post' => $post->load('tags')
        ]);
    }

    /**
     * Delete post
     */
    public function destroy(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        // Check authorization
        if ($post->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa bài viết này'
            ], 403);
        }

        if ($post->image && file_exists(public_path('uploads/forum/' . $post->image))) {
            unlink(public_path('uploads/forum/' . $post->image));
        }

        $post->delete();

        return response()->json([
            'message' => 'Bài viết đã được xóa'
        ]);
    }

    /**
     * Add comment
     */
    public function addComment(StoreForumCommentRequest $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        $comment = ForumComment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content
        ]);

        return response()->json([
            'message' => 'Bình luận đã được thêm',
            'comment' => $comment->load('user')
        ], 201);
    }

    /**
     * Delete comment
     */
    public function deleteComment(Request $request, $postId, $commentId)
    {
        $comment = ForumComment::where('id', $commentId)
            ->where('post_id', $postId)
            ->firstOrFail();

        // Check authorization
        if ($comment->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa bình luận này'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Bình luận đã được xóa'
        ]);
    }

    /**
     * Toggle like
     */
    public function toggleLike(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);
        $user = $request->user();

        $like = ForumLike::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'message' => 'Đã bỏ thích',
                'liked' => false
            ]);
        } else {
            ForumLike::create([
                'post_id' => $post->id,
                'user_id' => $user->id
            ]);
            return response()->json([
                'message' => 'Đã thích',
                'liked' => true
            ]);
        }
    }

    /**
     * Get all tags
     */
    public function tags()
    {
        $tags = ForumTag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->get();

        return response()->json([
            'tags' => $tags
        ]);
    }

    /**
     * Get posts by tag
     */
    public function getByTag($tagName)
    {
        $tag = ForumTag::where('name', $tagName)->firstOrFail();
        
        $posts = ForumPost::with(['user', 'tags'])
            ->whereHas('tags', function($q) use ($tag) {
                $q->where('forum_tags.id', $tag->id);
            })
            ->withCount(['comments', 'likes'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'tag' => $tag,
            'posts' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total()
            ]
        ]);
    }
}