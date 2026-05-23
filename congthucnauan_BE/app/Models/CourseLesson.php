<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $table = 'course_lessons';
    
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'video_url',
        'duration_minutes',
        'sort_order',
        'is_free',
        'image',
        'summary',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Relationships
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function completions()
    {
        return $this->hasMany(CourseLessonCompletion::class, 'lesson_id');
    }

    /**
     * Check if user completed this lesson
     */
    public function isCompletedByUser($userId)
    {
        return $this->completions()
                    ->where('user_id', $userId)
                    ->exists();
    }
}