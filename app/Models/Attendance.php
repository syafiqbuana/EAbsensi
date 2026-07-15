<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['student_id','schedule_id', 'date', 'time_in'];

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
