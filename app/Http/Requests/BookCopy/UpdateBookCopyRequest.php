<?php

namespace App\Http\Requests\BookCopy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookCopyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
            'barcode' => 'required|string|max:100|unique:book_copies,barcode,' . $this->route('book_copy'),
            'status' => 'required|in:available,borrowed,damaged,lost',
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'Book is required.',
            'book_id.exists' => 'Selected book does not exist.',
            'barcode.required' => 'Barcode is required.',
            'barcode.string' => 'Barcode must be a string.',
            'barcode.max' => 'Barcode must be less than 100 characters.',
            'barcode.unique' => 'Barcode already exists.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: available, borrowed, damaged, lost.',
        ];
    }
}
