<?php

namespace App\Models;

use App\Support\CurrentTpq;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $fillable = ['tpq_profile_id', 'name', 'order'];

    protected $casts = [
        'order' => 'integer',
    ];

    public static function booted() {

        static::creating(function ($classes){
            $classes->tpq_profile_id = CurrentTpq::Id();
            $classes->order = Classes::max('order') + 1;
        });
    }
    

    public function schedules()
    {
        return $this->belongsToMany(Schedules::class,'schedule_class','class_id','schedule_id')->withTimestamps();
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function scopeByTpqProfile($query, $tpqProfileId)
    {
        return $query->where('tpq_profile_id', $tpqProfileId);
    }

    public function studyRecords()
    {
        return $this->hasMany(StudyRecord::class, 'class_id');
    }
}
