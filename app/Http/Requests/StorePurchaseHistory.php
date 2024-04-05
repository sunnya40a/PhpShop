<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseHistory extends FormRequest
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
        $pattern = '/^[A-Z]{3} - \d{3}$/';
        return [
            'PO' => 'required|unique:purchaseHistory',
            'Pdate' => 'required|date',
            'item_list' => 'required|regex:' . $pattern,
            'description' => 'required',
            'qty' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1|max:9999',
            'category' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'PO' => 'Purchase Order',
            'Pdate' => 'Purchase Date',
            'item_list' => 'Item List',
            'description' => 'Description',
            'qty' => 'Quantity',
            'price' => 'Price',
            'user' => 'User',
            'category' => 'Category',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute has already been taken.',
            'numeric' => 'The :attribute must be a number.',
            'date' => 'The :attribute must be a valid date.',
            'min' => [
                'numeric' => 'The :attribute must be at least :min.',
                'string' => 'The :attribute must be at least :min characters.',
            ],
            'max' => [
                'numeric' => 'The :attribute may not be greater than :max.',
                'string' => 'The :attribute may not be greater than :max characters.',
            ],
            'email' => 'The :attribute must be a valid email address.',
            'alpha' => 'The :attribute may only contain letters.',
            'alpha_num' => 'The :attribute may only contain letters and numbers.',
            'required_with' => 'The :attribute field is required when :other is present.',
            'required_without' => 'The :attribute field is required when :values is not present.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'size' => 'The :attribute must be exactly :size characters long.',
            'in' => 'The selected :attribute is invalid.',
            'not_in' => 'The selected :attribute is invalid.',
            'regex' => 'The :attribute format is invalid.',
            'url' => 'The :attribute format is invalid.',
            'image' => 'The :attribute must be an image.',
            'file' => 'The :attribute must be a file.',
        ];
    }
}
