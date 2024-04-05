<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseHistory extends FormRequest
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
            'PO' => 'required',
            'Pdate' => 'required|date',
            'item_list' => 'required|regex:' . $pattern,
            'description' => 'required',
            'qty' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1|max:9999',
            'category' => 'required',
        ];
    }
}
