<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\QrCode;

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
            'qr_assignments.*.qr_code' => 'required|string|exists:qr_codes,code',
        ];
    }

    public function messages(): array
    {
        return [
            'qr_assignments.required' => 'Please assign at least one QR code.',
            'qr_assignments.*.order_item_id.required' => 'Order item is required.',
            'qr_assignments.*.order_item_id.exists' => 'Order item does not exist.',
            'qr_assignments.*.qr_code.required' => 'QR code is required.',
            'qr_assignments.*.qr_code.exists' => 'QR code does not exist.',
        ];
    }

    /**
     * Prepare data for validation - convert codes to IDs
     */
    protected function prepareForValidation()
    {
        if ($this->has('qr_assignments')) {
            $assignments = $this->qr_assignments;
            
            foreach ($assignments as $key => $assignment) {
                if (isset($assignment['qr_code'])) {
                    // Find QR code by code and get its ID
                    $qrCode = QrCode::where('code', $assignment['qr_code'])->first();
                    
                    if ($qrCode) {
                        $assignments[$key]['qr_code_id'] = $qrCode->id;
                    }
                }
            }
            
            $this->merge(['qr_assignments' => $assignments]);
        }
    }
}