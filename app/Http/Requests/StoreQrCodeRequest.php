<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQrCodeRequest extends FormRequest
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
            'foreground_color.regex' => 'The foreground color must be a valid hex color code (e.g., #000000).',
            'background_color.regex' => 'The background color must be a valid hex color code (e.g., #FFFFFF).',
            'logo.image' => 'The logo must be an image file.',
            'logo.max' => 'The logo size must not exceed 2MB.',
        ];
    }
}