<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'student_id',
        'approved_by',
        'created_by',
        'rejected_reason', // Ditambahkan ke fillable
        'start_date',
        'end_date',
        'total_days',
        'type',
        'description',
        'proof',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
        ];
    }

    public static function booted()
    {
        static::creating(function ($leaveRequest) {
            $leaveRequest->total_days = self::calculateTotalDays(
                $leaveRequest->student_id,
                $leaveRequest->start_date,
                $leaveRequest->end_date
            );
        });
    }

    public static function calculateTotalDays($studentId, $start_date, $end_date)
    {
        $student = Student::with('classes.schedules')->find($studentId);
        if (! $student?->classes?->schedules->isNotEmpty()) {
            return 0;
        }

        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);
        $schedules = $student->classes->schedules;
        $scheduleIds = $schedules->pluck('id')->toArray();

        $activeDays = self::extractActiveDays($schedules);

        // 3. Persiapkan lookup tanggal libur (O(1) time complexity)
        $holidayDates = self::prepareHolidayLookup($start, $end, $scheduleIds);
        $activeDays = self::extractActiveDays($schedules);

        $holidayDates = self::prepareHolidayLookup($start, $end, $scheduleIds);

        $totalDays = 0;
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            $dayOfWeek = $date->dayOfWeek;
            $dateString = $date->format('Y-m-d');

            // EARLY EXIT: Lewati jika BUKAN hari masuk sekolah ATAU merupakan hari libur
            if (! in_array($dayOfWeek, $activeDays) || isset($holidayDates[$dateString])) {
                continue;
            }

            // Jika lolos dari cek di atas, berarti ini hari masuk yang valid
            $totalDays++;
        }

        return $totalDays;
    }

    private static function extractActiveDays($schedules): array
    {
        $dayMap = [
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
        ];

        // Gunakan fungsi Collection Laravel agar rapi dan tanpa loop bersarang
        return $schedules->flatMap(fn ($schedule) => explode(',', $schedule->day))
            ->map(fn ($day) => $dayMap[$day] ?? null)
            ->filter() // Membuang nilai null jika ada
            ->unique()
            ->toArray();
    }

    private static function prepareHolidayLookup(Carbon $start, Carbon $end, array $scheduleIds): array
    {
        $holidays = Holiday::with('schedules')->where(function ($query) use ($start, $end) {
            $query->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere(function ($q) use ($start, $end) {
                    $q->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                });
        })->get();

        // Filter: Hanya ambil libur yang is_global = true ATAU jadwalnya beririsan dengan jadwal murid
        $applicableHolidays = $holidays->filter(function ($holiday) use ($scheduleIds) {
            if ($holiday->is_global) {
                return true;
            }

            $holidayScheduleIds = $holiday->schedules->pluck('id')->toArray();

            return ! empty(array_intersect($scheduleIds, $holidayScheduleIds));
        });

        // Mapping libur yang valid menjadi array lookup (Key-Value)
        $holidayLookup = [];
        foreach ($applicableHolidays as $holiday) {
            $period = CarbonPeriod::create($holiday->start_date, $holiday->end_date);
            foreach ($period as $date) {
                $holidayLookup[$date->format('Y-m-d')] = true;
            }
        }

        return $holidayLookup;
    }

    /**
     * Get the student that owns the leave request.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin user who approved/rejected the request.
     */
    public function approver(): BelongsTo
    {
        // Parameter kedua ('approved_by') perlu ditulis eksplisit
        // karena nama method (approver) berbeda dengan nama kolom (approved_by)
        return $this->belongsTo(User::class, 'approved_by');
    }
}
