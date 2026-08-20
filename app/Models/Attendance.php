<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PRESENT = 'present';

    public const STATUS_PERMISSION = 'permission';

    public const STATUS_SICK = 'sick';

    public const STATUS_ABSENT = 'absent';

    public const STATUS_HOLIDAY = 'holiday';

    protected $fillable = ['student_id', 'schedule_id', 'date', 'time_in', 'status'];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i:s',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedules::class);
    }
}
