<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email) {
        return User::where("email", $email)->first();
    }

    public function updateLastLogin(int $userId){
        return User::where("id", $userId)->update([
            "last_login" => now()
        ]);
    }

    public function updatePassword(int $userId, string $password){
        return User::where("id", $userId)->update([
            "password"       => Hash::make($password),
            "remember_token" => Str::random(60)
        ]);
    }
}
