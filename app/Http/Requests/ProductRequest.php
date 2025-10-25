<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required!',
            'category_id.exists' => 'Selected category not exist!',
            'name.required' => 'Product name is required!',
            'price.required' => 'Price is required!!',
            'price.numeric' => 'Price must be a number!',
            'price.min' => 'Price cannot be negative!',
            'stock.required' => 'Stock quantity is required!',
            'stock.integer' => 'Stock must be an integer!',
            'stock.min' => 'Stock cannot be negative!',
            'sku.required' => 'SKU is required!',
            'sku.unique' => 'This SKU is already in use!',
        ];
    }
}
