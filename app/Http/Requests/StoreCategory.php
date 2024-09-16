<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategory extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  //* I have changed it to true, timebeeing
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validation rule for 'category_code'
            'category_code' => [
                'required',
                'string',
                'regex:/^[A-Z]{3}$/',
                'size:3', // Category code should be exactly 3 characters long
                'unique:category_list,category_code', // Must be unique in the category_list table
            ],
            // Validation rule for 'description'
            'description' => [
                'required',
                'string',
                'max:255', // Description should be at most 255 characters long
                'unique:category_list,description', // Must be unique in the category_list table
            ],
        ];
    }

    public function attributes()
    {
        return [
            'category_code' => 'Category Code',
            'category' => 'Category',
        ];
    }
    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute has already been taken.',
            'max' => [
                'numeric' => 'The :attribute may not be greater than :max.',
                'string' => 'The :attribute may not be greater than :max characters.',
            ],
            'regex' => 'The :attribute format is invalid.',
            'size' => 'The :attribute must be exactly :size characters long.',
        ];
    }
}
