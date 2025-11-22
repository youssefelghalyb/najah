<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'customer_notes',
        'admin_notes',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'processed_by',
        'processed_at',
        'return_status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'return_status' => 'string',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('changed_at', 'desc');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['delivered']);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Accessors
     */
    public function getStatusBadgeClassAttribute()
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
        ][$this->payment_status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    public function getPaymentStatusLabelAttribute()
    {
        return ucfirst($this->payment_status);
    }

    /**
     * Helper Methods
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function canAssignQRCodes()
    {
        return $this->status === 'pending' && $this->payment_status === 'paid';
    }

    public function hasAllQRCodesAssigned()
    {
        $totalItems = $this->items->count();
        $assignedItems = $this->items->whereNotNull('qr_code_id')->count();

        return $totalItems === $assignedItems && $totalItems > 0;
    }

    public function getTotalItemsCount()
    {
        return $this->items->count();
    }

    public function getAssignedQRCodesCount()
    {
        return $this->items->whereNotNull('qr_code_id')->count();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;

        return 'ORD-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    /**
     * Check if return is pending
     */
    public function hasPendingReturn(): bool
    {
        return $this->status === 'refunded' && in_array($this->return_status, ['pending', 'approved']);
    }

    /**
     * Check if return is completed
     */
    public function isReturnCompleted(): bool
    {
        return $this->return_status === 'completed';
    }

    /**
     * Can change return status
     */
    public function canChangeReturnStatus(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Get return status badge class
     */
    public function getReturnStatusBadgeClassAttribute()
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-green-100 text-green-800',
        ][$this->return_status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get return status label
     */
    public function getReturnStatusLabelAttribute()
    {
        return $this->return_status ? ucfirst($this->return_status) : 'N/A';
    }
}
