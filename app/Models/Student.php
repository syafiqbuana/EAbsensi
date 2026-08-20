<?php

namespace App\Models;

use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable =
        [
            'name',
            'class_id',
            'birth_date',
            'gender',
            'birth_place',
            'qr_token',
        ];

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            $model->qr_token = uniqid();
        });
    }

    protected function countAge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->birth_date) {
                    return null;
                }
                $birthDate = $this->birth_date;
                $now = now();
                $years = (int) $birthDate->diffInYears($now, absolute: true);
                $days = (int) $birthDate->copy()->addYears($years)->diffInDays($now, absolute: true);

                return "{$years} tahun {$days} hari";
            },
        );
    }

    public function getQrCodeAttribute(): string
    {
        $builder = new Builder(
            writer: new PngWriter,
            data: $this->qr_token,
            size: 300,
            margin: 10,
        );

        $result = $builder->build();

        return $result->getDataUri();
    }

    protected function attendanceStatusToday(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Perhatikan penambahan ?-> di bawah ini
                $attendanceToday = $this->attendances?->firstWhere('date', Carbon::today()->toDateString());

                // Jika $this->attendances null, $attendanceToday juga akan otomatis null,
                // sehingga kode akan aman masuk ke pengecekan ini:
                if (! $attendanceToday) {
                    return 'Belum ada sesi';
                }

                return match ($attendanceToday->status) {
                    'pending' => 'Menunggu Absensi',
                    'present' => 'Hadir',
                    'sick' => 'Sakit',
                    'permission' => 'Izin',
                    'absent' => 'Alfa',
                    default => ucfirst($attendanceToday->status),
                };
            }
        );
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'student_user');
    }

    public function studyRecords()
    {
        return $this->hasMany(StudyRecord::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
