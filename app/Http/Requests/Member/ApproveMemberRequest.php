<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class ApproveMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'link_member_id' => 'nullable|integer|exists:members,id',
            'plan_id' => 'required_without:link_member_id|nullable|exists:member_plans,id',
            'expiry_date' => 'required_without:link_member_id|nullable|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required_without' => 'Plan is required.',
            'expiry_date.required_without' => 'Expiry date is required.',
            'expiry_date.after' => 'Expiry date must be in the future.',
        ];
    }
}
