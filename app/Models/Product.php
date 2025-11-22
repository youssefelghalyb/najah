<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'price',
        'discount_amount',
        'discount_type',
        'final_price',
        'stock_quantity',
        'is_visible',
        'stock_status',
        'low_stock_threshold',
        'status',
        'image_path',
        'gallery_images',
        'meta_title',
        'meta_description',
        'views_count',
        'orders_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_visible' => 'boolean',
        'low_stock_threshold' => 'integer',
        'views_count' => 'integer',
        'orders_count' => 'integer',
        'gallery_images' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            $product->final_price = $product->calculateFinalPrice();
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if ($product->isDirty(['price', 'discount_amount', 'discount_type'])) {
                $product->final_price = $product->calculateFinalPrice();
            }
            $product->updateStockStatus();
        });
    }

    /**
     * Calculate final price after discount
     */
    public function calculateFinalPrice(): float
    {
        if ($this->discount_amount <= 0) {
            return $this->price;
        }

        if ($this->discount_type === 'percentage') {
            return $this->price - ($this->price * ($this->discount_amount / 100));
        }

        return $this->price - $this->discount_amount;
    }

    /**
     * Update stock status based on quantity
     */
    public function updateStockStatus(): void
    {
        if ($this->stock_quantity <= 0) {
            $this->stock_status = 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->low_stock_threshold) {
            $this->stock_status = 'low_stock';
        } else {
            $this->stock_status = 'in_stock';
        }
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->price <= 0) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $this->discount_amount;
        }

        return ($this->discount_amount / $this->price) * 100;
    }

    /**
     * Get savings amount
     */
    public function getSavingsAmountAttribute(): float
    {
        return $this->price - $this->final_price;
    }

    /**
     * Check if product has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_status !== 'out_of_stock';
    }

    /**
     * Check if product is low stock
     */
    public function isLowStock(): bool
    {
        return $this->stock_status === 'low_stock';
    }

    /**
     * Check if product is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if product is visible
     */
    public function isVisible(): bool
    {
        return $this->is_visible;
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'card' => 'Card',
            'car_sticker' => 'Car Sticker',
            'bike_sticker' => 'Bike Sticker',
            default => $this->type,
        };
    }

    /**
     * Bundles that include this product
     */
    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'bundle_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for visible products
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for in stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0)
            ->where('stock_status', '!=', 'out_of_stock');
    }

    /**
     * Scope for products by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock_status', 'low_stock');
    }
}