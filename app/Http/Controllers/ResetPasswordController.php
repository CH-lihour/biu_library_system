<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function __construct(protected PasswordResetService $passwordResetService) {}

    public function index(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function store(ResetPasswordRequest $request)
    {
        $status = $this->passwordResetService->resetPassword($request->validated());

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'This link is invalid or has expired.']);
        }

        return to_route('login')->with('success', 'Your password was changed. Please sign in.');
    }
}
