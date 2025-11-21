<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class AssignQRCodesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_assignments' => 'required|array',
            'qr_assignments.*.order_item_id' => 'required|exists:order_items,id',
            'qr_assignments.*.qr_code_id' => 'required|exists:qr_codes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'qr_assignments.required' => 'Please assign at least one QR code.',
            'qr_assignments.*.order_item_id.required' => 'Order item is required.',
            'qr_assignments.*.order_item_id.exists' => 'Order item does not exist.',
            'qr_assignments.*.qr_code_id.required' => 'QR code is required.',
            'qr_assignments.*.qr_code_id.exists' => 'QR code does not exist.',
        ];
    }
}