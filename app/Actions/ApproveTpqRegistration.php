<?php

namespace App\Actions;

use App\Models\TpqProfile;
use App\Models\TpqRegistration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class ApproveTpqRegistration
{
    public static function execute(
        TpqRegistration $registration,
        User $reviewer
    ): TpqProfile {
        return DB::transaction(function () use ($registration, $reviewer) {

            $tpqProfile = TpqProfile::create([
                'slug' => TpqProfile::generateSlug(
                    $registration->tpq_registration_number,
                    $registration->tpq_name
                ),
                'name' => $registration->tpq_name,
                'registration_number' => $registration->tpq_registration_number,
                'address' => $registration->tpq_address,
                'contact_number' => $registration->tpq_contact_number,
                'status' => TpqProfile::STATUS_ACTIVE,
            ]);

            $registration->applicant->profile()->update([
                'tpq_profile_id' => $tpqProfile->id,
            ]);

            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($tpqProfile->id);

            $registration->applicant->assignRole('head_tpq');

            $registration->applicant->update([
                'is_active' => true,
            ]);

            $registration->update([
                'tpq_profile_id' => $tpqProfile->id,
                'status' => TpqRegistration::STATUS_APPROVED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            return $tpqProfile;
        });
    }
    public static function reject(TpqRegistration $registration, User $reviewer, ?string $reason = null): void
    {
        $registration->update([
            'status' => TpqRegistration::STATUS_REJECTED,
            'rejected_reason' => $reason,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }
}
