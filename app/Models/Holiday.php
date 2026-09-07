<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['tpq_profile_id', 'name', 'start_date', 'end_date', 'description', 'is_global'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_global' => 'boolean'
    ];

    public function schedules()
    {
        return $this->belongsToMany(Schedules::class,'holiday_schedule','holiday_id','schedule_id')->withTimestamps();
    }
}
