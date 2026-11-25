<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\RegistrationService;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(protected RegistrationService $registrationService) {}

    public function index()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        Auth::login($this->registrationService->register($request->validated()));
        $request->session()->regenerate();

        return to_route('portal.home')->with('success', 'Your account is ready. The library will now review your membership.');
    }
}
