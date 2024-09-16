<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventory extends FormRequest
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
            'category' => 'required|string|exists:category_list,description',
            'description' => 'required|string|max:255',
            'qty' => 'nullable|integer|min:0|max:999',
            'unit' => 'required|string|min:2|max:3',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'category' => 'Category description',
            'item_list' => 'Inventory code',
            'description' => 'Inventory description',
            'qty' => 'quantity',
            'unit' => 'unit of measurement',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category.required' => 'Please provide the category description for the inventory item.',
            'category.exists' => 'The category description you provided does not exist in the category list.',
            'description.required' => 'Please provide a description for the inventory item.',
            'description.string' => 'The inventory description must be a string.',
            'description.max' => 'The inventory description cannot exceed 255 characters.',
            'qty.integer' => 'The quantity must be a valid integer.',
            'qty.min' => 'The quantity cannot be less than 0.',
            'qty.max' => 'The quantity cannot be greater than 999.',
            'unit.required' => 'Please specify the unit of measurement for the inventory item.',
            'unit.string' => 'The unit of measurement must be a string.',
            'unit.min' => 'The unit of measurement must 2 to 3 characters long.',
            'unit.min' => 'The unit of measurement must 2 to 3 characters long.',
        ];
    }
}
