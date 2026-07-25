<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Student;
use App\Models\StudyRecord;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    public const PARENT_ROlE = 'parent';
    public const ADMIN_ROLE = 'admin';

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

        return match ($panel->getId()) {
            'superadmin' => $this->is_superadmin,

            'admin' => $this->is_active && ($this->is_superadmin || $this->hasRole(self::ADMIN_ROLE)), 

            default => false,
        };
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
                            $timeOpen = Carbon::parse($todaySchedule->time_open)->format('H:i');
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

    //Relation
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_user');
    }

    public function createdBy() {
        return $this->hasMany(LeaveRequest::class,'created_by');
    }

    public function studyRecords()
    {
        return $this->hasMany(StudyRecord::class, 'created_by');
    }

}

