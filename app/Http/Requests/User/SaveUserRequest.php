<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user');

        return [
            // users.name holds both names in 50 characters
            'firstname' => 'required|string|max:24',
            'lastname' => 'nullable|string|max:24',
            'email' => [
                'required', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($id),
                Rule::unique('staff', 'email')->ignore($id, 'user_id'),
            ],
            'phone' => 'nullable|string|max:15',
            'position' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,on_leave,terminated',
            'password' => [$id ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }

    public function attributes(): array
    {
        return ['firstname' => 'first name', 'lastname' => 'last name', 'role_id' => 'role'];
    }
}
