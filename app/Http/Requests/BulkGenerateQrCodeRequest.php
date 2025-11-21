<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkGenerateQrCodeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'count' => 'required|integer|min:1|max:1000',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'foreground_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'style' => 'nullable|in:square,dot,rounded',
            'size' => 'nullable|integer|min:100|max:1000',
            'error_correction' => 'nullable|in:L,M,Q,H',
            'expires_at' => 'nullable|date|after:now',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'count' => 'number of QR codes',
            'foreground_color' => 'foreground color',
            'background_color' => 'background color',
            'error_correction' => 'error correction level',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'count.required' => 'Please specify how many QR codes to generate.',
            'count.max' => 'You can generate a maximum of 1000 QR codes at once.',
            'foreground_color.regex' => 'The foreground color must be a valid hex color code (e.g., #000000).',
            'background_color.regex' => 'The background color must be a valid hex color code (e.g., #FFFFFF).',
        ];
    }
}