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
        return "https://qr.najaah.life/{$this->uuid}";
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
}