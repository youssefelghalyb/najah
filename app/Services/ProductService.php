<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Create a new product
     */
    public function create(array $data): Product
    {
        // Handle main image upload
        if (isset($data['image']) && $data['image']) {
            $data['image_path'] = $data['image']->store('products', 'public');
            unset($data['image']);
        }

        // Handle gallery images
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $galleryPaths = [];
            foreach ($data['gallery'] as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
            unset($data['gallery']);
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product = Product::create($data);

        return $product->fresh();
    }

    /**
     * Update product
     */
    public function update(Product $product, array $data): Product
    {
        // Handle main image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $data['image']->store('products', 'public');
            unset($data['image']);
        }

        // Handle gallery images
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $galleryPaths = $product->gallery_images ?? [];
            foreach ($data['gallery'] as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $data['gallery_images'] = $galleryPaths;
            unset($data['gallery']);
        }

        $product->update($data);

        return $product->fresh();
    }

    /**
     * Delete product
     */
    public function delete(Product $product): bool
    {
        // Delete images
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        if ($product->gallery_images) {
            foreach ($product->gallery_images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        return $product->delete();
    }

    /**
     * Delete gallery image
     */
    public function deleteGalleryImage(Product $product, string $imagePath): bool
    {
        $galleryImages = $product->gallery_images ?? [];
        
        if (($key = array_search($imagePath, $galleryImages)) !== false) {
            unset($galleryImages[$key]);
            Storage::disk('public')->delete($imagePath);
            $product->update(['gallery_images' => array_values($galleryImages)]);
            return true;
        }

        return false;
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Product $product, int $quantity, string $operation = 'set'): Product
    {
        if ($operation === 'add') {
            $product->stock_quantity += $quantity;
        } elseif ($operation === 'subtract') {
            $product->stock_quantity -= $quantity;
        } else {
            $product->stock_quantity = $quantity;
        }

        $product->updateStockStatus();
        $product->save();

        return $product->fresh();
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(array $productIds): int
    {
        $products = Product::whereIn('id', $productIds)->get();
        
        foreach ($products as $product) {
            $this->delete($product);
        }

        return $products->count();
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $productIds, string $status): int
    {
        return Product::whereIn('id', $productIds)->update(['status' => $status]);
    }

    /**
     * Duplicate product
     */
    public function duplicate(Product $product): Product
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = Str::slug($newProduct->name) . '-' . uniqid();
        $newProduct->save();

        return $newProduct;
    }
}