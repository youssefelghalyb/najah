<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BundleService
{
    /**
     * Create a new bundle
     */
    public function create(array $data): Bundle
    {
        return DB::transaction(function () use ($data) {
            // Extract products before creating bundle
            $productIds = $data['products'] ?? [];
            unset($data['products']);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                $data['image_path'] = $data['image']->store('bundles', 'public');
                unset($data['image']);
            }

            // Handle gallery images
            if (isset($data['gallery']) && is_array($data['gallery'])) {
                $galleryPaths = [];
                foreach ($data['gallery'] as $image) {
                    $galleryPaths[] = $image->store('bundles/gallery', 'public');
                }
                $data['gallery_images'] = $galleryPaths;
                unset($data['gallery']);
            }

            // Create bundle
            $bundle = Bundle::create($data);

            // Attach products
            if (!empty($productIds)) {
                $bundle->products()->attach($productIds);
            }

            return $bundle->load('products');
        });
    }

    /**
     * Update bundle
     */
    public function update(Bundle $bundle, array $data): Bundle
    {
        return DB::transaction(function () use ($bundle, $data) {
            // Extract products before updating
            $productIds = $data['products'] ?? [];
            unset($data['products']);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                // Delete old image
                if ($bundle->image_path) {
                    Storage::disk('public')->delete($bundle->image_path);
                }
                $data['image_path'] = $data['image']->store('bundles', 'public');
                unset($data['image']);
            }

            // Handle gallery images
            if (isset($data['gallery']) && is_array($data['gallery'])) {
                $existingGallery = $bundle->gallery_images ?? [];
                $newGalleryPaths = [];

                foreach ($data['gallery'] as $image) {
                    $newGalleryPaths[] = $image->store('bundles/gallery', 'public');
                }

                $data['gallery_images'] = array_merge($existingGallery, $newGalleryPaths);
                unset($data['gallery']);
            }

            // Update bundle
            $bundle->update($data);

            // Sync products
            if (!empty($productIds)) {
                $bundle->products()->sync($productIds);
            }

            return $bundle->load('products');
        });
    }

    /**
     * Delete bundle
     */
    public function delete(Bundle $bundle): bool
    {
        return DB::transaction(function () use ($bundle) {
            // Delete images
            if ($bundle->image_path) {
                Storage::disk('public')->delete($bundle->image_path);
            }

            if ($bundle->gallery_images) {
                foreach ($bundle->gallery_images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            // Detach products
            $bundle->products()->detach();

            // Delete bundle
            return $bundle->delete();
        });
    }

    /**
     * Delete a gallery image
     */
    public function deleteGalleryImage(Bundle $bundle, string $imagePath): bool
    {
        $galleryImages = $bundle->gallery_images ?? [];

        if (($key = array_search($imagePath, $galleryImages)) !== false) {
            unset($galleryImages[$key]);
            Storage::disk('public')->delete($imagePath);

            $bundle->update([
                'gallery_images' => array_values($galleryImages)
            ]);

            return true;
        }

        return false;
    }

    /**
     * Bulk delete bundles
     */
    public function bulkDelete(array $bundleIds): int
    {
        $bundles = Bundle::whereIn('id', $bundleIds)->get();

        foreach ($bundles as $bundle) {
            $this->delete($bundle);
        }

        return $bundles->count();
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $bundleIds, string $status): int
    {
        return Bundle::whereIn('id', $bundleIds)->update(['status' => $status]);
    }

    /**
     * Duplicate a bundle
     */
    public function duplicate(Bundle $bundle): Bundle
    {
        return DB::transaction(function () use ($bundle) {
            $newBundle = $bundle->replicate();
            $newBundle->name = $bundle->name . ' (Copy)';
            $newBundle->slug = Str::slug($newBundle->name) . '-' . time();
            $newBundle->views_count = 0;
            $newBundle->orders_count = 0;

            // Copy image if exists
            if ($bundle->image_path && Storage::disk('public')->exists($bundle->image_path)) {
                $extension = pathinfo($bundle->image_path, PATHINFO_EXTENSION);
                $newImagePath = 'bundles/' . Str::random(40) . '.' . $extension;
                Storage::disk('public')->copy($bundle->image_path, $newImagePath);
                $newBundle->image_path = $newImagePath;
            }

            // Copy gallery images
            if ($bundle->gallery_images) {
                $newGalleryPaths = [];
                foreach ($bundle->gallery_images as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                        $newImagePath = 'bundles/gallery/' . Str::random(40) . '.' . $extension;
                        Storage::disk('public')->copy($imagePath, $newImagePath);
                        $newGalleryPaths[] = $newImagePath;
                    }
                }
                $newBundle->gallery_images = $newGalleryPaths;
            }

            $newBundle->save();

            // Copy product relationships
            $productIds = $bundle->products->pluck('id')->toArray();
            $newBundle->products()->attach($productIds);

            return $newBundle->load('products');
        });
    }

    /**
     * Get bundle statistics
     */
    public function getStatistics(Bundle $bundle): array
    {
        $productsCount = $bundle->products->count();
        $totalPrice = $bundle->total_price;
        $finalPrice = $bundle->final_price;
        $savingsAmount = $bundle->savings_amount;

        return [
            'products_count' => $productsCount,
            'total_price' => $totalPrice,
            'final_price' => $finalPrice,
            'savings_amount' => $savingsAmount,
            'discount_percentage' => $bundle->discount_percentage,
            'average_price_per_product' => $productsCount > 0 ? $finalPrice / $productsCount : 0,
        ];
    }

    /**
     * Increment view count
     */
    public function incrementViews(Bundle $bundle): void
    {
        DB::table('bundles')->where('id', $bundle->id)->increment('views_count');
    }

    /**
     * Increment order count
     */
    public function incrementOrders(Bundle $bundle): void
    {
        DB::table('bundles')->where('id', $bundle->id)->increment('orders_count');
    }
}
