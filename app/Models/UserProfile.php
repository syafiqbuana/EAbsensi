<?php

namespace App\Models;

use App\Support\CurrentTpq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'tpq_profile_id',
        'full_name',
        'photo_path',
        'phone_number',
        'address',
    ];

    public static function booted() {
        static::creating(function($model) {
            $model->tp_profile_id = CurrentTpq::id();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo_path
            ? Storage::url($this->photo_path)
            : asset('images/default-avatar.png');
    }
}
