<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;

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
}
