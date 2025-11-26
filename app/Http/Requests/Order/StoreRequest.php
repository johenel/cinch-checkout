<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'email' => 'required|email',
            'address' => 'required',
            'orders' => 'required|array',
            'orders.*.product_id' => 'required|integer',
            'orders.*.product_name' => 'required|string',
            'orders.*.product_description' => 'nullable|string',
            'orders.*.product_featured_image' => 'nullable|string',
            'orders.*.product_price' => 'required|numeric',
            'orders.*.quantity' => 'required|min:1',
            'note' => 'nullable'
        ];
    }
}
