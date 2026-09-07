<?php

namespace App\Observers;

use App\Models\TpqProfile;
use App\Support\CurrentTpq;
use Illuminate\Support\Facades\Cache;

class TpqProfileObserver
{
    /**
     * Handle the TpqProfile "saved" event — invalidate cache.
     */
    public function saved(TpqProfile $tpqProfile): void
    {
        CurrentTpq::forgetCache($tpqProfile->slug);
    }

    /**
     * Guard against slug mutation after approval.
     */
    public function updating(TpqProfile $tpqProfile): void
    {
        if ($tpqProfile->isDirty('slug') && $tpqProfile->status === TpqProfile::STATUS_ACTIVE) {
            throw new \RuntimeException('Slug TPQ tidak boleh diubah setelah aktif.');
        }
    }
}
