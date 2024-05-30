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
            'material_desc' => 'required',
            'qty' => 'required|numeric|min:1',
            'unit' => 'required|string|max:3',
            'u_price' => 'required|numeric|min:1|max:9999',
            'p_price' => 'required|numeric|min:1|max:9999',
            'category' => 'string|required',
            'supplier_id' => 'numeric|required',
            'Rdate' => 'nullable|date',
            'paid_status' => 'nullable|numeric',
        ];
    }
}
