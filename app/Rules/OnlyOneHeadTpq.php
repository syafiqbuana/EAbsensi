<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\Permission\PermissionRegistrar;

class OnlyOneHeadTpq implements ValidationRule
{
    public function __construct(private int $tpqProfileId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($this->tpqProfileId);

        $existingHead = User::role('head_tpq')->first();

        if ($existingHead && $existingHead->id !== $value) {
            $fail('TPQ ini sudah memiliki Kepala TPQ. Hanya boleh 1 Kepala TPQ per TPQ.');
        }
    }
}
