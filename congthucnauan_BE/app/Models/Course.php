<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    
    protected $fillable = [
        'title',
        'description',
        'price',
        'duration',
        'level',
        'classroom_id',
        'user_id',
        'image',
        'status',
        'requirements',
        'what_will_learn',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
    ];

    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Relationships
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class)->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
                    ->withPivot('enrollment_date', 'progress', 'status', 'payment_id');
    }

    /**
     * Accessors
     */
    public function getTotalStudentsAttribute()
    {
        return $this->enrollments()->count();
    }

    public function getTotalLessonsAttribute()
    {
        return $this->lessons()->count();
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}