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
            // Extract products and stock quantity before creating bundle
            $products = $data['products'] ?? [];
            $bundleQuantity = $data['stock_quantity'] ?? 0;
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

            // Attach products with quantities
            if (!empty($products)) {
                $syncData = [];
                foreach ($products as $productData) {
                    $productId = is_array($productData) ? $productData['id'] : $productData;
                    $quantity = is_array($productData) ? ($productData['quantity'] ?? 1) : 1;
                    
                    $syncData[$productId] = ['quantity' => $quantity];
                }
                $bundle->products()->sync($syncData);
            }

            // Deduct stock from products
            if ($bundleQuantity > 0) {
                $this->deductProductStock($bundle, $bundleQuantity);
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
            // Get old and new quantities
            $oldQuantity = $bundle->stock_quantity;
            $newQuantity = $data['stock_quantity'] ?? $oldQuantity;
            
            // Extract products before updating
            $products = $data['products'] ?? null;
            unset($data['products']);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
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

            // Sync products if provided
            if ($products !== null) {
                $oldProducts = $bundle->products()->get();
                
                $syncData = [];
                foreach ($products as $productData) {
                    $productId = is_array($productData) ? $productData['id'] : $productData;
                    $quantity = is_array($productData) ? ($productData['quantity'] ?? 1) : 1;
                    
                    $syncData[$productId] = ['quantity' => $quantity];
                }
                
                // Return stock from old configuration
                $this->returnProductStock($bundle, $oldQuantity, $oldProducts);
                
                // Sync new products
                $bundle->products()->sync($syncData);
                $bundle->load('products');
                
                // Deduct stock for new configuration
                $this->deductProductStock($bundle, $newQuantity);
            } else {
                // Only quantity changed, adjust accordingly
                $difference = $newQuantity - $oldQuantity;
                
                if ($difference > 0) {
                    // Increasing quantity - deduct more
                    $this->deductProductStock($bundle, $difference);
                } elseif ($difference < 0) {
                    // Decreasing quantity - return some
                    $this->returnProductStock($bundle, abs($difference));
                }
            }

            return $bundle->fresh(['products']);
        });
    }

    /**
     * Update bundle stock quantity
     */
    public function updateStock(Bundle $bundle, int $quantity): Bundle
    {
        return DB::transaction(function () use ($bundle, $quantity) {
            $oldQuantity = $bundle->stock_quantity;
            $difference = $quantity - $oldQuantity;

            if ($difference > 0) {
                // Increasing quantity - deduct from products
                $this->deductProductStock($bundle, $difference);
            } elseif ($difference < 0) {
                // Decreasing quantity - return to products
                $this->returnProductStock($bundle, abs($difference));
            }

            $bundle->update(['stock_quantity' => $quantity]);

            return $bundle->fresh(['products']);
        });
    }

    /**
     * Deduct product stock when creating/increasing bundle stock
     */
    protected function deductProductStock(Bundle $bundle, int $bundleQuantity): void
    {
        foreach ($bundle->products as $product) {
            $requiredQuantity = ($product->pivot->quantity ?? 1) * $bundleQuantity;
            
            // Check if enough stock
            if ($product->stock_quantity < $requiredQuantity) {
                throw new \Exception(
                    "Insufficient stock for product '{$product->name}'. " .
                    "Required: {$requiredQuantity}, Available: {$product->stock_quantity}"
                );
            }
            
            $product->decrement('stock_quantity', $requiredQuantity);
            $product->updateStockStatus();
            $product->save();
        }
    }

    /**
     * Return product stock when decreasing/deleting bundle stock
     */
    protected function returnProductStock(Bundle $bundle, int $bundleQuantity, $products = null): void
    {
        $products = $products ?? $bundle->products;
        
        foreach ($products as $product) {
            $returnQuantity = ($product->pivot->quantity ?? 1) * $bundleQuantity;
            $product->increment('stock_quantity', $returnQuantity);
            $product->updateStockStatus();
            $product->save();
        }
    }

    /**
     * Delete bundle
     */
    public function delete(Bundle $bundle): bool
    {
        return DB::transaction(function () use ($bundle) {
            // Return all stock to products
            if ($bundle->stock_quantity > 0) {
                $this->returnProductStock($bundle, $bundle->stock_quantity);
            }

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
            $newBundle->stock_quantity = 0; // Start with 0 stock
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

            // Copy product relationships with quantities
            $syncData = [];
            foreach ($bundle->products as $product) {
                $syncData[$product->id] = ['quantity' => $product->pivot->quantity ?? 1];
            }
            $newBundle->products()->sync($syncData);

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
            'stock_quantity' => $bundle->stock_quantity,
            'actual_stock_quantity' => $bundle->actual_stock_quantity,
            'stock_status' => $bundle->stock_status,
            'limiting_products' => $bundle->getLimitingProducts()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_quantity' => $product->stock_quantity,
                    'required_quantity' => $product->pivot->quantity ?? 1,
                ];
            }),
        ];
    }

    /**
     * Check if bundle can be created with given quantity
     */
    public function canCreateWithQuantity(Bundle $bundle, int $quantity): array
    {
        $issues = [];

        foreach ($bundle->products as $product) {
            $requiredQuantity = ($product->pivot->quantity ?? 1) * $quantity;
            
            if ($product->stock_quantity < $requiredQuantity) {
                $issues[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'required' => $requiredQuantity,
                    'available' => $product->stock_quantity,
                    'shortage' => $requiredQuantity - $product->stock_quantity,
                ];
            }
        }

        return [
            'can_create' => empty($issues),
            'issues' => $issues,
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