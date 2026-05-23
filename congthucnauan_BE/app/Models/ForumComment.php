<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $table = 'forum_comments';
    
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
    ];

    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Relationships
     */
    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumComment::class, 'parent_id');
    }

    /**
     * Scopes
     */
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }
}