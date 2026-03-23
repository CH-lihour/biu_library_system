<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(protected AuthRepositoryInterface $authRepository){}

    public function login(array $credentials, bool $remember = false) : bool
    {
        if (!Auth::attempt($credentials, $remember)) {
            return false;
        }

        $this->authRepository->updateLastLogin(auth()->id());

        return true;
    }

    public function logout() : void
    {
        Auth::logout();
    }

    public function redirectByRole(): string
    {
        return match(auth()->user()->role) {
            'admin' => route('dashboard'),
            'librarian' => route('dashboard'),
            'member' => route('dashboard'),
            default => route('dashboard'),
        };
    }
}
