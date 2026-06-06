<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'role',
        'email_verified_at',
        'email_verification_code',
        'email_verification_code_expires_at',
        'is_email_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_code_expires_at' => 'datetime',
            'is_email_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Helper methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'user_roles','user_id','role_id')
                ->withPivot('branch_id');
    }

    public function hasRole(string $roleName , ?int $branchId = null)
    {
        if ($this->role === $roleName) return true;

        return $this->roles()
            ->where('name',$roleName)
            ->when($branchId, fn($q) => $q->wherePivot('branch_id',$branchId))
            ->exists();
    }

    public function hasPermission(string $permissionCode, ?int $branchId = null) : bool
    {
        if ($this->hasRole('admin'))
            return true;

        return $this->roles()
            ->when($branchId, fn($q) => $q->wherePivot('branch_id', $branchId))
            ->whereHas('permissions', fn($q) => $q->where('code', $permissionCode))
            ->exists();
    }

}
