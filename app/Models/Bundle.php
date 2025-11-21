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
    ];

    /**
     * Relationships
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_product')
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

    /**
     * Accessors - Dynamic Price Calculations
     */
    
    // Sum of all current product base prices
    public function getTotalPriceAttribute()
    {
        return $this->products->sum('price');
    }

    // Final bundle price after discount
    public function getFinalPriceAttribute()
    {
        $totalPrice = $this->total_price;
        
        if ($this->discount_type === 'percentage') {
            return $totalPrice - ($totalPrice * ($this->discount_amount / 100));
        }
        
        return max(0, $totalPrice - $this->discount_amount);
    }

    // How much customer saves
    public function getSavingsAmountAttribute()
    {
        return max(0, $this->total_price - $this->final_price);
    }

    // Discount as percentage
    public function getDiscountPercentageAttribute()
    {
        if ($this->total_price == 0) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $this->discount_amount;
        }

        // Convert fixed discount to percentage
        return ($this->savings_amount / $this->total_price) * 100;
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