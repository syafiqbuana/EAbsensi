<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Student;
use App\Models\StudyRecord;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Support\CurrentTpq;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_active',
        'address',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const PARENT_ROLE = 'parent';
    public const TEACHER_ROLE = 'teacher';
    public const HEAD_TPQ_ROLE = 'head_tpq';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'superadmin' => $this->isSuperAdmin(),
            'admin'      => $this->isSuperAdmin() || $this->checkAdminPanelAccess(),
            default      => false,
        };
    }

    protected function checkAdminPanelAccess(): bool
    {
        $currentTpqId = CurrentTpq::id();

        if ($currentTpqId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($currentTpqId);
            $this->unsetRelation('roles');
        }

        return $this->hasAnyRole([self::HEAD_TPQ_ROLE, self::TEACHER_ROLE]);
    }

    public function scopeByTpqProfile(Builder $query, int|string $tpqId): Builder
    {
        return $query->whereHas('roles', function (Builder $q) use ($tpqId) {
            $q->where('model_has_roles.team_id', $tpqId);
        });
    }

public function scopeHeadTpq(Builder $query, int|string $tpqId): Builder
    {
        return $query->whereHas('roles', function (Builder $q) use ($tpqId) {
            $q->where('name', self::HEAD_TPQ_ROLE)
            ->where('model_has_roles.team_id', $tpqId);
        });
    }


    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function tpqRegistrations(): HasMany
    {
        return $this->hasMany(TpqRegistration::class, 'applicant_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_user');
    }

    public function createdBy()
    {
        return $this->hasMany(LeaveRequest::class, 'created_by');
    }

    public function studyRecords()
    {
        return $this->hasMany(StudyRecord::class, 'created_by');
    }

    // ─── Multi-tenant Helpers ─────────────────────────────

    /**
     * Cek apakah user adalah Superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    /**
     * Cek apakah user adalah Kepala TPQ di TPQ tertentu.
     */
    public function isHeadOfTpq(int $tpqProfileId): bool
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($tpqProfileId);
        return $this->hasRole('head_tpq');
    }

    /**
     * Cek apakah user adalah Teacher di TPQ tertentu.
     */
    public function isTeacherOf(int $tpqProfileId): bool
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($tpqProfileId);
        return $this->hasRole('teacher');
    }

    /**
     * Ambil semua TPQ yang dimiliki/diakses user ini.
     */
    public function accessibleTpqProfiles(): Collection
    {
        $teamIds = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_type', static::class)
            ->where('model_id', $this->id)
            ->whereNotNull('team_id')
            ->pluck('team_id')
            ->unique();

        return TpqProfile::whereIn('id', $teamIds)->get();
    }

    /**
     * Ambil TpqProfile utama (untuk redirect setelah login universal).
     */
    public function primaryTpqProfile(): ?TpqProfile
    {
        $teamId = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_type', static::class)
            ->where('model_id', $this->id)
            ->whereNotNull('team_id')
            ->value('team_id');

        return $teamId ? TpqProfile::find($teamId) : null;
    }

    /**
     * Cek apakah registrasi TPQ user sedang pending.
     */
    public function hasPendingRegistration(): bool
    {
        return $this->tpqRegistrations()
            ->where('status', TpqRegistration::STATUS_PENDING)
            ->exists();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->profile?->full_name ?? $this->name;
    }

    protected function todayScheduleReminder(): Attribute
    {
        return Attribute::make(
            get: function () {
                // 1. Ambil semua murid milik user ini
                $students = $this->students;

                if (! $students || $students->isEmpty()) {
                    return null;
                }

                $dayName = strtolower(Carbon::today()->format('l'));
                $reminders = []; // Array untuk menampung teks jadwal masing-masing anak

                // 2. Looping setiap murid untuk mencari jadwalnya
                foreach ($students as $student) {
                    $class = $student->classes?->first(); // Mengambil kelas pertama dari murid

                    if ($class && $class->schedules) {
                        // 3. Cari jadwal hari ini (dengan perbaikan explode koma)
                        $todaySchedule = $class->schedules->first(function ($schedule) use ($dayName) {
                            $daysArray = array_map('trim', explode(',', $schedule->day ?? ''));
                            return in_array($dayName, $daysArray);
                        });

                        // 4. Jika ada jadwal, format string-nya dan masukkan ke array
                        if ($todaySchedule) {
                            $timeOpen  = Carbon::parse($todaySchedule->time_open)->format('H:i');
                            $timeClose = Carbon::parse($todaySchedule->time_close)->format('H:i');

                            // Hasilnya misal: Fulan: "TPQ Sore" (15:00 - 17:00)
                            $reminders[] = "{$student->name} {$todaySchedule->name} ({$timeOpen} - {$timeClose})";
                        }
                    }
                }

                // 5. Jika tidak ada satu pun anak yang punya jadwal hari ini
                if (empty($reminders)) {
                    return null;
                }

                // 6. Gabungkan semua jadwal anak menjadi satu kalimat
                // Menggunakan pemisah " | " agar rapi dibaca jika ada banyak anak
                return $reminders;
            }
        );
    }
}
