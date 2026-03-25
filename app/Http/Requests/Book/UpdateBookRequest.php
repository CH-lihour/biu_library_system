<?php

namespace App\Http\Requests\Book;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isbn'            => 'required|string|max:20|unique:books,isbn,' . $this->route('book'),
            'title'           => 'required|string|max:200',
            'publisher_id'    => 'required|exists:publishers,id',
            'category_id'     => 'required|exists:categories,id',
            'publish_year'    => 'nullable|integer|min:1000|max:' . date('Y'),
            'pages'           => 'nullable|integer|min:1',
            'language'        => 'nullable|string|max:50',
            'shelf_location'  => 'nullable|string|max:100',
            'cover_image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
