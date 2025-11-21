<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreBundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bundles,slug',
            'description' => 'nullable|string|max:5000',
            'discount_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'status' => 'nullable|in:active,inactive,archived',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'products' => 'required|array|min:2',
            'products.*' => 'required|exists:products,id',
        ];

        // When updating, ignore current bundle's slug
        if ($this->route('bundle')) {
            $rules['slug'] = 'nullable|string|max:255|unique:bundles,slug,' . $this->route('bundle')->id;
        }

        // Validate percentage discount
        if ($this->discount_type === 'percentage') {
            $rules['discount_amount'] = 'required|numeric|min:0|max:100';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bundle name is required.',
            'discount_amount.required' => 'Discount amount is required.',
            'discount_amount.min' => 'Discount amount must be at least 0.',
            'discount_amount.max' => 'Percentage discount cannot exceed 100%.',
            'discount_type.required' => 'Discount type is required.',
            'discount_type.in' => 'Discount type must be either fixed or percentage.',
            'products.required' => 'Please select at least 2 products for the bundle.',
            'products.min' => 'A bundle must contain at least 2 products.',
            'products.*.required' => 'Product is required.',
            'products.*.exists' => 'Selected product does not exist.',
        ];
    }

    protected function prepareForValidation()
    {
        // Auto-generate slug if not provided
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }

        // Set default status if not provided
        if (!$this->status) {
            $this->merge([
                'status' => 'active',
            ]);
        }

        // Set default discount if not provided
        if (!$this->discount_amount) {
            $this->merge([
                'discount_amount' => 0,
            ]);
        }

        if (!$this->discount_type) {
            $this->merge([
                'discount_type' => 'fixed',
            ]);
        }
    }
}