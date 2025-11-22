<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class QrCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'code',
        'title',
        'description',
        'logo_path',
        'foreground_color',
        'background_color',
        'style',
        'size',
        'error_correction',
        'qr_image_path',
        'status',
        'scan_count',
        'last_scanned_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'scan_count' => 'integer',
        'size' => 'integer',
        'last_scanned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($qrCode) {
            if (empty($qrCode->uuid)) {
                $qrCode->uuid = (string) Str::uuid();
            }

            if (empty($qrCode->code)) {
                $qrCode->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique 5-digit code
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the full QR URL
     */
    public function getFullUrlAttribute(): string
    {
        return "http://najah.local:8381/{$this->uuid}";
    }

    /**
     * Get the QR display code (formatted)
     */
    public function getFormattedCodeAttribute(): string
    {
        return chunk_split($this->code, 5, '-');
    }

    /**
     * Check if QR code is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Check if QR code is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Increment scan count
     */
    public function incrementScanCount(): void
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    /**
     * User who created this QR code
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active QR codes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for expired QR codes
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope for user's QR codes
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Get order items that use this QR code
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'qr_code_id');
    }

    /**
     * Get the order item this QR is assigned to (if any)
     */
    public function orderItem()
    {
        return $this->hasOne(OrderItem::class, 'qr_code_id');
    }

    /**
     * Check if QR code is assigned to an order
     */
    public function isAssigned(): bool
    {
        return $this->orderItems()->exists();
    }

    /**
     * Get the order this QR code belongs to (through order item)
     */
    public function order()
    {
        return $this->hasOneThrough(
            Order::class,
            OrderItem::class,
            'qr_code_id', // Foreign key on order_items table
            'id',         // Foreign key on orders table
            'id',         // Local key on qr_codes table
            'order_id'    // Local key on order_items table
        );
    }

    /**
     * Get the profile linked to this QR code
     */
    public function profile()
    {
        return $this->belongsToMany(Profile::class, 'profile_qr_code')
            ->withPivot('linked_at', 'linked_by', 'notes')
            ->withTimestamps()
            ->first();
    }

    /**
     * Get all profiles ever linked to this QR code (if you track history)
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_qr_code')
            ->withPivot('linked_at', 'linked_by', 'notes')
            ->withTimestamps();
    }

    /**
     * Check if QR code has a profile linked
     */
    public function hasProfile(): bool
    {
        return $this->profiles()->exists();
    }

    /**
     * Get the profile linked to this QR code
     */
    public function getLinkedProfile()
    {
        return $this->profiles()->first();
    }

    /**
     * Link QR code to profile
     */
    public function linkToProfile(int $profileId, ?int $linkedBy = null, ?string $notes = null): void
    {
        // First unlink from any existing profile
        $this->unlinkFromProfile();

        // Then link to new profile
        $this->profiles()->attach($profileId, [
            'linked_at' => now(),
            'linked_by' => $linkedBy,
            'notes' => $notes,
        ]);
    }

    /**
     * Unlink QR code from any profile
     */
    public function unlinkFromProfile(): void
    {
        $this->profiles()->detach();
    }

    /**
     * Check if QR code is available for linking (not assigned to order and no profile)
     */
    public function isAvailableForLinking(): bool
    {
        return !$this->isAssigned() && !$this->hasProfile();
    }

    /**
     * Scope for QR codes without profiles
     */
    public function scopeWithoutProfile($query)
    {
        return $query->whereDoesntHave('profiles');
    }

    /**
     * Scope for QR codes with profiles
     */
    public function scopeWithProfile($query)
    {
        return $query->whereHas('profiles');
    }
}
