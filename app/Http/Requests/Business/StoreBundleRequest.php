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
            'stock_quantity' => 'required|integer|min:0',
            'stock_quantity_alert' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'products' => 'required|array|min:2',
            'products.*' => 'required|exists:products,id',
            
            // Image uploads
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'gallery' => 'nullable|array|max:5',
            'gallery.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
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
            'name.max' => 'Bundle name cannot exceed 255 characters.',
            
            'slug.unique' => 'This slug is already in use.',
            'slug.max' => 'Slug cannot exceed 255 characters.',
            
            'discount_amount.required' => 'Discount amount is required.',
            'discount_amount.min' => 'Discount amount must be at least 0.',
            'discount_amount.max' => 'Percentage discount cannot exceed 100%.',
            'discount_amount.numeric' => 'Discount amount must be a number.',
            
            'discount_type.required' => 'Discount type is required.',
            'discount_type.in' => 'Discount type must be either fixed or percentage.',
            
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            
            'stock_quantity_alert.integer' => 'Alert threshold must be a whole number.',
            'stock_quantity_alert.min' => 'Alert threshold cannot be negative.',
            
            'is_featured.boolean' => 'Featured status must be true or false.',
            
            'products.required' => 'Please select at least 2 products for the bundle.',
            'products.min' => 'A bundle must contain at least 2 products.',
            'products.array' => 'Products must be an array.',
            'products.*.required' => 'Product is required.',
            'products.*.exists' => 'Selected product does not exist.',
            
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Image must be jpeg, jpg, png, or webp format.',
            'image.max' => 'Image size cannot exceed 2MB.',
            
            'gallery.array' => 'Gallery must be an array of images.',
            'gallery.max' => 'You can upload a maximum of 5 gallery images.',
            'gallery.*.image' => 'All gallery files must be images.',
            'gallery.*.mimes' => 'Gallery images must be jpeg, jpg, png, or webp format.',
            'gallery.*.max' => 'Each gallery image cannot exceed 2MB.',
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
        if ($this->discount_amount === null) {
            $this->merge([
                'discount_amount' => 0,
            ]);
        }

        if (!$this->discount_type) {
            $this->merge([
                'discount_type' => 'fixed',
            ]);
        }

        // Set default stock quantity alert if not provided
        if ($this->stock_quantity_alert === null) {
            $this->merge([
                'stock_quantity_alert' => 10,
            ]);
        }


        if ($this->has('is_featured')) {
            $this->merge([
                'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN),
            ]);
        } else {
            $this->merge([
                'is_featured' => false,
            ]);
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'bundle name',
            'slug' => 'URL slug',
            'description' => 'description',
            'discount_amount' => 'discount amount',
            'discount_type' => 'discount type',
            'stock_quantity' => 'stock quantity',
            'stock_quantity_alert' => 'low stock alert threshold',
            'is_featured' => 'featured status',
            'products' => 'products',
            'products.*' => 'product',
            'image' => 'bundle image',
            'gallery' => 'gallery images',
            'gallery.*' => 'gallery image',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation()
    {
        // Additional validation: Check if stock_quantity is reasonable
        if ($this->stock_quantity > 10000) {
            $this->validator->errors()->add(
                'stock_quantity',
                'Stock quantity seems unusually high. Please verify this is correct.'
            );
        }

        // Warning if stock alert is higher than stock quantity
        if ($this->stock_quantity_alert > $this->stock_quantity) {
            // This is just a warning, not blocking validation
            session()->flash('warning', 'Low stock alert threshold is higher than the current stock quantity.');
        }
    }
}