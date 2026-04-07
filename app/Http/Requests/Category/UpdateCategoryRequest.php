<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:categories,slug,' . $this->route('category'),
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must be less than 100 characters.',
            'slug.required' => 'Slug is required.',
            'slug.string' => 'Slug must be a string.',
            'slug.max' => 'Slug must be less than 100 characters.',
            'slug.unique' => 'Slug already exists.',
            'description.string' => 'Description must be a string.',
        ];
    }
}
