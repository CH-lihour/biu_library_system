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
        'role',
        'is_verified',
        'last_login',
        'reset_token',
    ];

    protected $hidden = [
        'password',
        'reset_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'last_login' => 'datetime',
    ];

    public function staff(){
        return $this->hasOne(Staff::class);
    }
}
