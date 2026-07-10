<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $fillable = ['name','order'];

    protected $casts = [
        'order' => 'integer',
    ];

    public function schedules()
    {
        return $this->belongsToMany(Schedules::class,'schedule_class');
    }

    public function studyRecords()
    {
        return $this->hasMany(StudyRecord::class, 'class_id');
    }
}
