<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(protected AuthRepositoryInterface $authRepository){}

    public function login(array $credentails) :bool
    {
        if (!Auth::attempt($credentails)) {
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
            'admin' => route('admin.dashboard'),
            'librarian' => route('librarian.dashboard'),
            'member' => route('member.dashboard'),
            default => route('dashboard'),
        };
    }
}
