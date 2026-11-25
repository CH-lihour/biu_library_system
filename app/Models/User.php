<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_verified',
        'last_login',
        'reset_token',
    ];

    protected $hidden = [
        'password',
        'reset_token',
        'remember_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'last_login'  => 'datetime',
    ];

    public function staff(){
        return $this->hasOne(Staff::class);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function member(){
        return $this->hasOne(Member::class);
    }

    /** Staff work in the admin panel: they have a role and are still employed. */
    public function isStaff(): bool
    {
        return $this->role_id !== null && $this->staff?->status !== 'terminated';
    }

    /** Members use the member front-end. */
    public function isMember(): bool
    {
        return $this->member !== null;
    }

    public function isSuperAdmin(): bool
    {
        return $this->isStaff() && (bool) $this->role?->is_super;
    }

    /** Whether this user may open the given named admin route. */
    public function canAccess(string $route): bool
    {
        if (! $this->isStaff()) {
            return false;
        }

        return $this->isSuperAdmin() || in_array($route, $this->role?->routes() ?? [], true);
    }
}
