<?php

namespace App\Http\Requests\MemberPlan;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberPlanRequest extends FormRequest
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
    * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'loan_duration_days' => ['required', 'integer', 'min:1'],
            'fine_per_day' => ['required', 'numeric', 'min:0', 'max:5.99'],
            'discount_fee' => ['required', 'numeric', 'min:0', 'max:10.99'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Plan name is required.',
            'name.string' => 'Plan name must be a string.',
            'name.max' => 'Plan name must not exceed 100 characters.',
            'loan_duration_days.required' => 'Loan duration is required.',
            'loan_duration_days.integer' => 'Loan duration must be an integer.',
            'loan_duration_days.min' => 'Loan duration must be at least 1 day.',
            'fine_per_day.required' => 'Fine per day is required.',
            'fine_per_day.numeric' => 'Fine per day must be a number.',
            'fine_per_day.min' => 'Fine per day must be at least 0.',
            'fine_per_day.max' => 'Fine per day must not exceed 5.99.',
            'discount_fee.required' => 'Discount fee is required.',
            'discount_fee.numeric' => 'Discount fee must be a number.',
            'discount_fee.min' => 'Discount fee must be at least 0.',
            'discount_fee.max' => 'Discount fee must not exceed 10.99.',
            'description.string' => 'Description must be a string.',
        ];
    }
}
