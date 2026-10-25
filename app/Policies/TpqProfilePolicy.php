<?php

namespace App\Policies;

use App\Models\TpqProfile;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class TpqProfilePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function update(User $user, TpqProfile $tpqProfile): bool
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($tpqProfile->id);

        return $user->can('manage tpq profile');
    }
}