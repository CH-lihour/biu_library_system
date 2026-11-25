<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\PasswordResetService;

class ForgotPasswordController extends Controller
{
    public function __construct(protected PasswordResetService $passwordResetService) {}

    public function index(){
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request)
    {
        $this->passwordResetService->sendResetLink($request->validated('email'));

        return back()->with('success', 'If that email exists, a reset link was sent.');
    }
}
