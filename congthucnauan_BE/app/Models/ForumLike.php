<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumLike extends Model
{
    protected $table = 'forum_likes';
    
    protected $fillable = [
        'post_id',
        'user_id',
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
}