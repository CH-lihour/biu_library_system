<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'is_super' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Every named route this role opens. Meaningless for a super role, which opens all.
     *
     * @return array<int, string>
     */
    public function routes(): array
    {
        return $this->permissions->pluck('routes')->flatten()->unique()->values()->all();
    }
}
