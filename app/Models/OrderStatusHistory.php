<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Accessors
     */
    public function getOldStatusLabelAttribute()
    {
        return $this->old_status ? ucfirst($this->old_status) : 'New Order';
    }

    public function getNewStatusLabelAttribute()
    {
        return ucfirst($this->new_status);
    }

    public function getStatusChangeTextAttribute()
    {
        $old = $this->old_status_label;
        $new = $this->new_status_label;
        
        return "{$old} → {$new}";
    }
}