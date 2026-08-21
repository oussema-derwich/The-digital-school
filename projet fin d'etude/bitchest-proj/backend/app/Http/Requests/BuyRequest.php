<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cryptocurrency_id' => 'required|integer|exists:cryptos,id',
            'quantity' => 'required|numeric|min:0.00000001',
            'price' => 'required|numeric|min:0.01',
        ];
    }

    public function messages()
    {
        return [
            'cryptocurrency_id.required' => 'Cryptocurrency ID is required.',
            'cryptocurrency_id.exists' => 'Invalid cryptocurrency.',
            'quantity.required' => 'Quantity is required.',
            'quantity.numeric' => 'Quantity must be a valid number.',
            'quantity.min' => 'Quantity must be greater than 0.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be greater than 0.',
        ];
    }
}
