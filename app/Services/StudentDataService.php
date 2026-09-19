<?php

namespace App\Services;

use App\Models\Student;
use App\Support\CurrentTpq;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class StudentDataService
{
    public static function getActiveStudentsForUser(int $userId, ?int $tpqProfileId = null): Collection
    {
        $tpqProfileId = $tpqProfileId ?? CurrentTpq::id();
        $cacheKey = "active_students.user_{$userId}.tpq_{$tpqProfileId}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId, $tpqProfileId) {
            return Student::query()
                ->with(['classes.schedules'])
                ->whereHas('users', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->where('status', Student::STATUS_ACTIVE)
                ->when($tpqProfileId, function ($query, $tpqId) {
                    $query->where('tpq_profile_id', $tpqId);
                })
                ->get();
        });
    }
}