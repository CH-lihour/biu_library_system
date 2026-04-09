<?php

namespace App\Http\Requests\BookTransaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_copy_id' => ['required', 'exists:book_copies,id'],
            'member_id' => ['required', 'exists:members,id'],
            'borrow_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:borrow_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_copy_id.required' => 'Book copy is required.',
            'book_copy_id.exists' => 'Selected book copy does not exist.',
            'member_id.required' => 'Member is required.',
            'member_id.exists' => 'Selected member does not exist.',
            'borrow_date.required' => 'Borrow date is required.',
            'borrow_date.date' => 'Borrow date must be a valid date.',
            'due_date.required' => 'Due date is required.',
            'due_date.date' => 'Due date must be a valid date.',
            'due_date.after_or_equal' => 'Due date must be after or equal to the borrow date.',
        ];
    }
}
