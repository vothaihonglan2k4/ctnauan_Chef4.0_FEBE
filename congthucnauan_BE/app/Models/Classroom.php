<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'classrooms';
    
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'location',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Relationships
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}