<?php

namespace App\Models;

use App\Support\CurrentTpq;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['tpq_profile_id', 'name', 'start_date', 'end_date', 'description', 'is_global'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_global' => 'boolean'
    ];

    

    public static function booted()
    {
        static::creating(function ($model) {
            $model->tpq_profile_id = CurrentTpq::Id();
        });
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedules::class,'holiday_schedule','holiday_id','schedule_id')->withTimestamps();
    }
}
