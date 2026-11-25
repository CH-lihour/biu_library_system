<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
    }

    public function rules(): array
    {
        return [
            'token'    => 'required|string',
            'email'    => 'required|email|max:150',
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'This reset link is not valid. Please request a new one.',
            'email.required'     => 'Email is required',
            'email.email'        => 'Please enter a valid email',
            'password.required'  => 'Password is required',
            'password.confirmed' => 'The passwords do not match.',
        ];
    }
}
