<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLessonCompletion extends Model
{
    protected $table = 'course_lesson_completion';
    
    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public $timestamps = false;
    
    const CREATED_AT = 'completed_at';
    const UPDATED_AT = null;

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }
}