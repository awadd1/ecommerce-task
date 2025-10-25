<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'items' => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'notes' => ['nullable', 'string', 'max:500'],
            ];
        }

        return [
            'status' => ['required', 'string', Rule::in(OrderStatus::values())],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Order must contain at least one item!',
            'items.array' => 'Items must be an array!',
            'items.min' => 'Order must contain at least one item!',
            'items.*.product_id.required' => 'Product ID is required for each item!',
            'items.*.product_id.exists' => 'One or more products do not exist!',
            'items.*.quantity.required' => 'Quantity is required for each item!',
            'items.*.quantity.min' => 'Quantity must be at least 1!',
            'status.required' => 'Status is required!',
            'status.in' => 'Invalid status value!',
        ];
    }
}
