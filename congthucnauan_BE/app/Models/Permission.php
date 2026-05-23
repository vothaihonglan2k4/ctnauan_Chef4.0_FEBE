<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;

    protected $fillable = ['code','module','action','display_name'];

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permissions','permission_id','role_id');
    }
}
