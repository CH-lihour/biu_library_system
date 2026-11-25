<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One thing a role can allow. PermissionSyncService keeps the list in step with the admin routes. */
class Permission extends Model
{
    protected $fillable = [
        'name',
        'group',
        'routes',
    ];

    protected $casts = [
        'routes' => 'array',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
