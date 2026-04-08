<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
            'plan_id' => 'required|exists:member_plans,id',
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('members', 'email')
                    ->ignore($this->route('member'))
                    ->whereNull('deleted_at')
            ],
            'join_date' => 'required|date',
            'expiry_date' => 'required|date|after:join_date',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|min:10|max:15',
            'status' => 'default:active',
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Plan is required.',
            'plan_id.exists' => 'Selected plan does not exist.',
            'member_code.unique' => 'Member code already exists.',
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must be less than 100 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must be less than 255 characters.',
            'email.unique' => 'Email already exists.',
            'join_date.required' => 'Join date is required.',
            'join_date.date' => 'Join date must be a valid date.',
            'expiry_date.required' => 'Expiry date is required.',
            'expiry_date.date' => 'Expiry date must be a valid date.',
            'expiry_date.after' => 'Expiry date must be after join date.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address must be less than 255 characters.',
            'phone.string' => 'Phone must be a string.',
            'phone.min' => 'Phone must be at least 10 digits.',
            'phone.max' => 'Phone must be less than 15 digits.',
        ];
    }
}
