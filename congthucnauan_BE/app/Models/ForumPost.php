<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $table = 'forum_posts';
    
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
        'video_url',
        'recipe_id',
        'views',
        'is_pinned',
        'status',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'views' => 'integer',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(ForumLike::class, 'post_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ForumTag::class, 'forum_post_tags', 'post_id', 'tag_id');
    }

    /**
     * Accessors
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', 1);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    /**
     * Helper methods
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}