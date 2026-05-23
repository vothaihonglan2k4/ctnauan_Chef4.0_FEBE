<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $table = 'course_enrollments';
    
    protected $fillable = [
        'user_id',
        'course_id',
        'payment_id',
        'enrollment_date',
        'completion_date',
        'progress',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
        'completion_date' => 'datetime',
        'progress' => 'integer',
    ];

    public $timestamps = false;

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

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if enrollment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed' || $this->progress >= 100;
    }
}