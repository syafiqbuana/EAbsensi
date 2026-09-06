<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

class AuthenticateFilamentAdmin extends FilamentAuthenticate
{
    protected function redirectTo($request): ?string
    {
        $slug = $request->route('tpq_slug');

        if ($slug) {
            return route('filament.admin.auth.login', [
                'tpq_slug' => $slug,
            ]);
        }

        if ($slug = session('current_tpq_slug')) {
            return route('filament.admin.auth.login', [
                'tpq_slug' => $slug,
            ]);
        }

        return url('/universal/admin/login');
    }
}