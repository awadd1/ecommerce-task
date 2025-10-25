<?php

namespace App\Http\Requests;

use App\Enums\WarehouseTransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockAdjustmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->canManageProducts();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'string', Rule::in(WarehouseTransactionType::values())],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required!',
            'product_id.exists' => 'Product does not exist!',
            'type.required' => 'Transaction type is required!',
            'type.in' => 'Invalid transaction type!',
            'quantity.required' => 'Quantity is required!',
            'quantity.min' => 'Quantity must be at least 1!',
        ];
    }
}