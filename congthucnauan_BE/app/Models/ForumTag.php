<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTag extends Model
{
    protected $table = 'forum_tags';
    
    protected $fillable = [
        'name',
        'slug',
    ];

    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Relationships
     */
    public function posts()
    {
        return $this->belongsToMany(ForumPost::class, 'forum_post_tags', 'tag_id', 'post_id');
    }

    /**
     * Get posts count for this tag
     */
    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }
}