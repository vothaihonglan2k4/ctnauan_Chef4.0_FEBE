<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipes';
    public $timestamps = false;
    
    protected $fillable = [
        'title',
        'description',
        'ingredients',
        'instructions',
        'image',
        'video_url',
        'category_id',
        'user_id',
        'status',
    ];

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Accessors
     */
    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 1);
    }

    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}