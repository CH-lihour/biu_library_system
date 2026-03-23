<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;


class LoginController extends Controller
{
    public function __construct(protected AuthService $authService){}

    public function index(){
        return view("auth.login");
    }

    public function store(LoginRequest $request)
    {
        if (! $this->authService->login(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->authService->redirectByRole());
    }

    public function destroy(Request $request){
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
