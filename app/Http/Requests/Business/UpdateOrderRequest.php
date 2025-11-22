<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Customer Information
            'customer_name' => 'sometimes|required|string|max:255',
            'customer_email' => 'nullable|email|max:255', 
            'customer_phone' => 'sometimes|required|string|max:20',
            'customer_address' => 'nullable|string|max:1000',
            'return_status' => 'nullable|in:pending,approved,rejected,completed',
            // Payment
            'payment_method' => 'nullable|string|max:50',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',

            // Status
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled,refunded',

            // Shipping
            'tracking_number' => 'nullable|string|max:100',

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
            'status.in' => 'Invalid order status.',
            'payment_status.in' => 'Invalid payment status.',
        ];
    }
}
