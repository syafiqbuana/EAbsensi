<?php

namespace App\Models;

use App\Models\Student;
use App\Models\TpqRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TpqProfile extends Model
{
    use SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'slug',
        'name',
        'registration_number',
        'address',
        'contact_number',
        'logo_path',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(TpqRegistration::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedules::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class);
    }

    public static function generateSlug(string $registrationNumber, string $name): string
    {
        $name = str_replace(' ', '', $name);

        return strtoupper(
            Str::slug($registrationNumber . '-' . $name)
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }
}
