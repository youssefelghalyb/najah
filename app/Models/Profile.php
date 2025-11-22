<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Profile extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'age',
        'date_of_birth',
        'email',
        'password',
        'phone',
        'address',
        'height',
        'weight',
        'blood_type',
        'allergies',
        'emergency_contacts',
        'chronic_conditions',
        'current_medications',
        'medical_history',
        'medical_files',
        'important_note',
        'profile_image',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'age' => 'integer',
        'date_of_birth' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'emergency_contacts' => 'array',
        'chronic_conditions' => 'array',
        'current_medications' => 'array',
        'medical_files' => 'array',
        'last_login_at' => 'datetime',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($profile) {
            if (empty($profile->uuid)) {
                $profile->uuid = (string) Str::uuid();
            }

            if (!empty($profile->password) && !Hash::isHashed($profile->password)) {
                $profile->password = Hash::make($profile->password);
            }
        });

        static::updating(function ($profile) {
            if ($profile->isDirty('password') && !Hash::isHashed($profile->password)) {
                $profile->password = Hash::make($profile->password);
            }
        });
    }

    /**
     * Relationships
     */
    
    /**
     * Get QR codes linked to this profile
     */
    public function qrCodes()
    {
        return $this->belongsToMany(QrCode::class, 'profile_qr_code')
            ->withPivot('linked_at', 'linked_by', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the primary QR code (most recently linked)
     */
    public function primaryQrCode()
    {
        return $this->belongsToMany(QrCode::class, 'profile_qr_code')
            ->withPivot('linked_at')
            ->orderByPivot('linked_at', 'desc')
            ->limit(1);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Accessors
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image 
            ? Storage::url($this->profile_image)
            : null;
    }

    public function getMedicalFileUrlsAttribute()
    {
        if (!$this->medical_files) {
            return [];
        }

        return collect($this->medical_files)->map(function ($path) {
            return Storage::url($path);
        })->toArray();
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getAgeFromDobAttribute()
    {
        if (!$this->date_of_birth) {
            return $this->age;
        }

        return now()->diffInYears($this->date_of_birth);
    }

    public function getBmiAttribute()
    {
        if (!$this->height || !$this->weight) {
            return null;
        }

        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters * $heightInMeters), 2);
    }

    /**
     * Helper Methods
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasQrCodes(): bool
    {
        return $this->qrCodes()->exists();
    }

    public function hasMultipleQrCodes(): bool
    {
        return $this->qrCodes()->count() > 1;
    }

    public function getQrCodesCount(): int
    {
        return $this->qrCodes()->count();
    }

    /**
     * Link QR code to profile
     */
    public function linkQrCode(int $qrCodeId, ?int $linkedBy = null, ?string $notes = null): void
    {
        $this->qrCodes()->attach($qrCodeId, [
            'linked_at' => now(),
            'linked_by' => $linkedBy,
            'notes' => $notes,
        ]);
    }

    /**
     * Unlink QR code from profile
     */
    public function unlinkQrCode(int $qrCodeId): void
    {
        $this->qrCodes()->detach($qrCodeId);
    }

    /**
     * Check if QR code is linked to this profile
     */
    public function hasQrCode(int $qrCodeId): bool
    {
        return $this->qrCodes()->where('qr_codes.id', $qrCodeId)->exists();
    }

    /**
     * Get emergency contacts formatted
     */
    public function getEmergencyContactsFormattedAttribute()
    {
        if (!$this->emergency_contacts) {
            return [];
        }

        return collect($this->emergency_contacts)->map(function ($contact, $index) {
            return [
                'id' => $index + 1,
                'name' => $contact['name'] ?? '',
                'phone' => $contact['phone'] ?? '',
            ];
        });
    }

    /**
     * Get chronic conditions formatted
     */
    public function getChronicConditionsFormattedAttribute()
    {
        if (!$this->chronic_conditions) {
            return [];
        }

        return collect($this->chronic_conditions)->map(function ($condition, $index) {
            return [
                'id' => $index + 1,
                'name' => $condition['name'] ?? '',
                'status' => $condition['status'] ?? '',
            ];
        });
    }

    /**
     * Get current medications formatted
     */
    public function getCurrentMedicationsFormattedAttribute()
    {
        if (!$this->current_medications) {
            return [];
        }

        return collect($this->current_medications)->map(function ($medication, $index) {
            return [
                'id' => $index + 1,
                'name' => $medication['name'] ?? '',
                'dosage' => $medication['dosage'] ?? '',
                'frequency' => $medication['frequency'] ?? '',
            ];
        });
    }

    /**
     * Update last login
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}