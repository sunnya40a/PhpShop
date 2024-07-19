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
        // $itemList = $this->route('inventory'); // Get the item_list from the route parameter
        // return [
        //     'category' => 'required|string|exists:category_list,description',
        //     'item_list' => 'required|string|regex:/^[A-Z]{3} - \d{3}$/|exists:inventory,item_list,' . $itemList . ',item_list',
        //     'description' => 'required|string|max:255',
        //     'qty' => 'nullable|integer|min:0|max:999',
        //     'unit' => 'required|string|size:3',
        // ];
        return [
            'category' => 'required|string|exists:category_list,description',
            'description' => 'required|string|max:255',
            'qty' => 'nullable|integer|min:0|max:999',
            'unit' => 'required|string|size:3',
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
            'unit.size' => 'The unit of measurement must be exactly 3 characters long.',
        ];
    }
}
