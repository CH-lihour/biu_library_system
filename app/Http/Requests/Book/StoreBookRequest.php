<?php

namespace App\Http\Requests\Book;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isbn'             => 'required|string|max:20|unique:books',
            'title'            => 'required|string|max:200',
            'publisher_id'     => 'required|exists:publishers,id',
            'category_id'      => 'required|exists:categories,id',
            'publish_year'     => 'required|integer|min:1000|max:' . date('Y'),
            'language'         => 'required|string|max:50',
            'cover_image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',  
            'shelf_location'   => 'nullable|string|max:100', 
            'pages'            => 'nullable|integer|min:1',  
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.required'          => 'ISBN is required.',      
            'isbn.unique'            => 'ISBN already exists.',
            'title.required'         => 'Title is required.',
            'title.max'              => 'Title must be less than 200 characters.',
            'publisher_id.required'  => 'Publisher is required.',  
            'publisher_id.exists'    => 'Publisher does not exist.',
            'category_id.required'   => 'Category is required.',
            'category_id.exists'     => 'Category does not exist.',
            'publish_year.required'  => 'Publish year is required.',
            'publish_year.integer'   => 'Publish year must be a number.',
            'cover_image_url.image'  => 'Cover image must be a valid image file.',
            'cover_image_url.mimes'  => 'Cover image must be a jpg, jpeg, png, or webp file.',
            'language.required'      => 'Language is required.',
            'shelf_location.string'  => 'Shelf location must be a string.',
        ];
    }

}
