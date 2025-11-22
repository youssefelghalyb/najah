<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Bundle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'discount_amount',
        'discount_type',
        'status',
        'stock_quantity',
        'stock_quantity_alert',
        'is_featured',
        'image_path',
        'gallery_images',
        'meta_title',
        'meta_description',
        'views_count',
        'orders_count',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'stock_quantity' => 'integer',
        'stock_quantity_alert' => 'integer',
        'is_featured' => 'boolean',
        'gallery_images' => 'array',
        'views_count' => 'integer',
        'orders_count' => 'integer',
    ];

    protected $appends = [
        'total_price',
        'final_price',
        'discount_percentage',
        'savings_amount',
        'actual_stock_quantity',
        'stock_status',
    ];

    /**
     * Relationships
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }


    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Price Calculations
     */
    
    public function getTotalPriceAttribute()
    {
        return $this->products->sum('price');
    }

    public function getFinalPriceAttribute()
    {
        $totalPrice = $this->total_price;
        
        if ($this->discount_type === 'percentage') {
            return $totalPrice - ($totalPrice * ($this->discount_amount / 100));
        }
        
        return max(0, $totalPrice - $this->discount_amount);
    }

    public function getSavingsAmountAttribute()
    {
        return max(0, $this->total_price - $this->final_price);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->total_price == 0) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $this->discount_amount;
        }

        return ($this->savings_amount / $this->total_price) * 100;
    }

    /**
     * Stock Management - The actual available quantity based on products
     */
    public function getActualStockQuantityAttribute()
    {
        if ($this->products->isEmpty()) {
            return 0;
        }

        // Calculate maximum bundles that can be made from available products
        $maxBundles = $this->products->map(function ($product) {
            $requiredQuantity = $product->pivot->quantity ?? 1;
            
            if ($requiredQuantity <= 0) {
                return PHP_INT_MAX; // No limit if no quantity required
            }
            
            return floor($product->stock_quantity / $requiredQuantity);
        })->min();

        // Return the lesser of: bundle's set quantity OR what products can fulfill
        return min($this->stock_quantity, $maxBundles ?? 0);
    }

    /**
     * Get stock status based on actual available quantity
     */
    public function getStockStatusAttribute()
    {
        $actualStock = $this->actual_stock_quantity;
        
        if ($actualStock <= 0) {
            return 'out_of_stock';
        }
        
        if ($actualStock <= $this->stock_quantity_alert) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }

    /**
     * Check if bundle can be fulfilled
     */
    public function canFulfill(int $quantity = 1): bool
    {
        return $this->actual_stock_quantity >= $quantity;
    }

    /**
     * Get products that are limiting bundle stock
     */
    public function getLimitingProducts()
    {
        return $this->products->filter(function ($product) {
            $requiredQuantity = $product->pivot->quantity ?? 1;
            $canMake = floor($product->stock_quantity / $requiredQuantity);
            return $canMake < $this->stock_quantity;
        });
    }

    /**
     * Decrement bundle stock and product stocks
     */
    public function decrementStock(int $quantity = 1): bool
    {
        if (!$this->canFulfill($quantity)) {
            return false;
        }

        // Decrement bundle quantity
        $this->decrement('stock_quantity', $quantity);

        // Decrement each product's quantity
        foreach ($this->products as $product) {
            $requiredQuantity = ($product->pivot->quantity ?? 1) * $quantity;
            $product->decrement('stock_quantity', $requiredQuantity);
            $product->updateStockStatus();
        }

        return true;
    }

    /**
     * Increment bundle stock and product stocks (for returns/cancellations)
     */
    public function incrementStock(int $quantity = 1): void
    {
        // Increment bundle quantity
        $this->increment('stock_quantity', $quantity);

        // Increment each product's quantity
        foreach ($this->products as $product) {
            $requiredQuantity = ($product->pivot->quantity ?? 1) * $quantity;
            $product->increment('stock_quantity', $requiredQuantity);
            $product->updateStockStatus();
        }
    }

    /**
     * Helper Methods
     */
    public function hasDiscount()
    {
        return $this->discount_amount > 0;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isFeatured()
    {
        return $this->is_featured;
    }


    public function isInStock()
    {
        return $this->actual_stock_quantity > 0;
    }

    public function isLowStock()
    {
        return $this->stock_status === 'low_stock';
    }

    /**
     * Image Handling
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path 
            ? Storage::url($this->image_path)
            : null;
    }

    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery_images) {
            return [];
        }

        return collect($this->gallery_images)->map(function ($path) {
            return Storage::url($path);
        })->toArray();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });

        static::updating(function ($bundle) {
            if ($bundle->isDirty('name') && empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });
    }
}