<?php

namespace App\Models;

use App\Support\CurrentTpq;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    protected $fillable = ['tpq_profile_id', 'name', 'day', 'time_open', 'time_close'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->tpq_profile_id = CurrentTpq::Id();
        });
    }

    protected function day(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? explode(',', $value) : [],
            set: fn ($value) => is_array($value) ? implode(',', $value) : $value,
        );
    }

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
