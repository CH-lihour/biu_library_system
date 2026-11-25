<?php

namespace App\Services;

use App\Models\User;
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

        // Neither a working staff account nor a member: nothing to sign in to.
        if (! auth()->user()->isStaff() && ! auth()->user()->isMember()) {
            Auth::logout();

            return false;
        }

        $this->authRepository->updateLastLogin(auth()->id());

        return true;
    }

    public function logout() : void
    {
        Auth::logout();
    }

    public function home(User $user): string
    {
        return $user->isStaff() ? route('dashboard') : route('portal.home');
    }

    /** The page asked for before signing in, unless it belongs to the other front-end. */
    public function redirectAfterLogin(): string
    {
        $user = auth()->user();
        $intended = session()->pull('url.intended');
        $isPortal = $intended && str_starts_with(trim((string) parse_url($intended, PHP_URL_PATH), '/').'/', 'my/');

        if (! $intended || (! $user->isStaff() && ! $isPortal)) {
            return $this->home($user);
        }

        return $intended;
    }
}
