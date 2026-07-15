<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    protected $fillable =['name','day','time_open','time_close'];




    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'schedule_class', 'schedule_id', 'class_id')->withTimestamps();
    }

    public function holidays()
    {
        return $this->belongsToMany(Holiday::class, 'holiday_schedule', 'schedule_id', 'holiday_id')->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
