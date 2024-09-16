<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplier extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //* I have changed it to true, timebeeing
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            's_name' => 'required|string|max:30|unique:suppliers,s_name',
            'mobile1' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'mobile2' => 'nullable|string|size:10|regex:/^[0-9]{10}$/',
            'c_person' => 'required|string|max:20',
            'contact_info' => 'nullable|string|max:30',
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
            's_name' => 'Supplier Name',
            'mobile1' => 'Mobile no.(1)',
            'mobile2' => 'Mobile no.(2)',
            'c_person' => 'Contact Person',
            'contact_info' => 'Contact Information',
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
            's_name.required' => 'Please provide the supplier name.',
            's_name.unique' => 'The supplier name has already been taken.',
            's_name.max' => 'The supplier name cannot exceed 30 characters.',
            'mobile1.required' => 'Please provide the mobile number (1) for the supplier.',
            'mobile1.regex' => 'Mobile number (1) should be in a 10-digit format with digits only.',
            'mobile2.size' => 'Mobile number (2) should be exactly 10 digits.',
            'mobile2.regex' => 'Mobile number (2) should be in a 10-digit format with digits only.',
            'c_person.required' => 'Please provide the contact person’s name for the supplier.',
            'c_person.max' => 'The contact person’s name cannot exceed 20 characters.',
            'contact_info.max' => 'The contact information cannot exceed 30 characters.',
        ];
    }
}
