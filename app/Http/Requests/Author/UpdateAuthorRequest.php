<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
    * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:authors,email,' . $this->route('author'),
            'bio' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => 'First name is required.',
            'firstname.string' => 'First name must be a string.',
            'firstname.max' => 'First name must be less than 50 characters.',
            'lastname.required' => 'Last name is required.',
            'lastname.string' => 'Last name must be a string.',
            'lastname.max' => 'Last name must be less than 50 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must be less than 100 characters.',
            'email.unique' => 'Email already exists.',
            'bio.string' => 'Bio must be a string.',
        ];
    }
}
