<?php

namespace App\Http\Requests\Publisher;

use Illuminate\Foundation\Http\FormRequest;

class StorePublisherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:publishers,name' . $this->route('publisher'),
            'email' => 'required|email|max:100|unique:publishers,email' . $this->route('publisher'),
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must be less than 100 characters.',
            'name.unique' => 'Name already exists.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must be less than 100 characters.',
            'email.unique' => 'Email already exists.',
            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a string.',
            'phone.max' => 'Phone number must be less than 15 characters.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address must be less than 255 characters.',
        ];
    }
}
