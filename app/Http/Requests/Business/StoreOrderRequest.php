<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Customer Information
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:1000',
            
            // Order Items
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:product,bundle',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            
            // Pricing (optional, can be calculated)
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            
            // Payment
            'payment_method' => 'nullable|string|max:50',
            'payment_status' => 'nullable|in:pending,paid,failed',
            
            // Notes
            'customer_notes' => 'nullable|string|max:2000',
            'admin_notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Customer name is required.',
            'customer_email.required' => 'Customer email is required.',
            'customer_email.email' => 'Please provide a valid email address.',
            'customer_phone.required' => 'Customer phone number is required.',
            'items.required' => 'Please add at least one item to the order.',
            'items.min' => 'Order must contain at least one item.',
            'items.*.item_type.required' => 'Item type is required.',
            'items.*.item_type.in' => 'Item type must be either product or bundle.',
            'items.*.item_id.required' => 'Item ID is required.',
            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    protected function prepareForValidation()
    {
        // Set default payment status if not provided
        if (!$this->payment_status) {
            $this->merge([
                'payment_status' => 'pending',
            ]);
        }

        // Set default amounts if not provided
        if (!$this->discount_amount) {
            $this->merge(['discount_amount' => 0]);
        }
        
        if (!$this->tax_amount) {
            $this->merge(['tax_amount' => 0]);
        }
        
        if (!$this->shipping_amount) {
            $this->merge(['shipping_amount' => 0]);
        }
    }
}