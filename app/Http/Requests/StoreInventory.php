<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventory extends FormRequest
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
            'item_list' => 'nullable|string|size:9|regex:/^[A-Z]{3} - \d{3}$/|unique:inventory,item_list',
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
        return
            [
                'category.required' => 'Please provide the category description for the inventory item.',
                'category.exists' => 'The category description you provided does not exist in the categories table.',
                'item_list.regex' => 'The item list code must follow the format: "XXX - 000", where "XXX" is the category code and "000" is a 3-digit number.',
                'item_list.unique' => 'The item list code you provided is already in use. Please choose a different code.',
                'description.required' => 'Please provide a description for the inventory item.',
                'description.string' => 'The inventory description must be a string.',
                'description.max' => 'The inventory description cannot exceed 255 characters.',
                'qty.required' => 'Please specify the quantity of the inventory item.',
                'qty.integer' => 'The quantity must be a valid integer.',
                'qty.min' => 'The quantity cannot be less than 0.',
                'qty.max' => 'The quantity cannot be more than 999.',
                'unit.required' => 'Please specify the unit of measurement for the inventory item.',
                'unit.string' => 'The unit of measurement must be a string.',
                'unit.min' => 'The unit of measurement must be 2 to 3 characters long.',
                'unit.max' => 'The unit of measurement must be 2 to 3 characters long.',
            ];
    }
}
