<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    protected $fillable =['name','day','time_open','time_close'];

    protected $casts = [
        'time_open' => 'datetime:H:i:s',
        'time_close' => 'datetime:H:i:s',
    ];

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'schedule_class');
    }

    
}
