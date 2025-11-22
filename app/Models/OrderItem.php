<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'item_name',
        'item_description',
        'unit_price',
        'quantity',
        'discount_amount',
        'total',
        'qr_code_id',
        'qr_assigned_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'qr_assigned_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'item_id');
    }

    /**
     * Get the actual item (product or bundle)
     */
    public function getItemAttribute()
    {
        if ($this->item_type === 'product') {
            return Product::find($this->item_id);
        } elseif ($this->item_type === 'bundle') {
            return Bundle::find($this->item_id);
        }

        return null;
    }

    /**
     * Accessors
     */
    public function getItemTypeLabelAttribute()
    {
        return ucfirst($this->item_type);
    }

    /**
     * Helper Methods
     */
    public function hasQRCodeAssigned()
    {
        return !is_null($this->qr_code_id);
    }

    public function assignQRCode($qrCodeId)
    {
        $this->update([
            'qr_code_id' => $qrCodeId,
            'qr_assigned_at' => now(),
        ]);
    }

    public function unassignQRCode()
    {
        $this->update([
            'qr_code_id' => null,
            'qr_assigned_at' => null,
        ]);
    }
}