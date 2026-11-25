<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Password;

class PasswordResetService
{
    public function __construct(protected AuthRepositoryInterface $authRepository) {}

    public function sendResetLink(string $email)
    {
        return Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $data)
    {
        return Password::reset($data, function(User $user, string $password) {
            $this->authRepository->updatePassword($user->id, $password);
        });
    }
}
