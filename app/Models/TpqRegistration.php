<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TpqRegistration extends Model
{
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'applicant_id',
        'tpq_profile_id',
        'status',
        'registration_number',
        'tpq_name',
        'tpq_registration_number',
        'tpq_address',
        'tpq_contact_number',
        'notes',
        'rejected_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ─── Accessors ────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING  => 'Menunggu Persetujuan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default               => $this->status,
        };
    }

    public static function generateRegistrationNumber(string $tpqName): string
{
    $name = strtoupper($tpqName);

    $name = preg_replace('/^TPQ\s+/i', '', $name);
    $name = preg_replace('/[^A-Z0-9]/i', '', $name);

    do {
        $randomNumber = random_int(100000, 999999);
        $registrationNumber = "REG-{$name}-{$randomNumber}";
    } while (self::where('registration_number', $registrationNumber)->exists());

    return $registrationNumber;
}

    // ─── Status Checks ────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // ─── Relasi ───────────────────────────────────────────

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function tpqProfile(): BelongsTo
    {
        return $this->belongsTo(TpqProfile::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
