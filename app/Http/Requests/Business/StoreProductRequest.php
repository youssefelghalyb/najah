<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string|max:5000',
            'type' => 'required|in:card,car_sticker,bike_sticker',
            'price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive,archived',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];

        // When updating, ignore current product's slug
        if ($this->route('product')) {
            $rules['slug'] = 'nullable|string|max:255|unique:products,slug,' . $this->route('product')->id;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'type.required' => 'Product type is required.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be at least 0.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'Image size must not exceed 2MB.',
        ];
    }
}