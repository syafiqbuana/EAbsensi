<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\TpqRegistration;
use App\Support\CurrentTpq;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;


class Login extends BaseLogin
{



public function mount(): void
{
    // Jika sudah login, redirect sesuai kondisi
    if (Filament::auth()->check()) {
        $user = Filament::auth()->user();

        if ($user?->isSuperAdmin()) {
            $this->redirect(url('/superadmin'), navigate: false);
            return;
        }

        $panel = Filament::getCurrentPanel();

        // Coba resolve TPQ dari session, CurrentTpq, atau primaryTpqProfile milik user
        $tpqProfile = CurrentTpq::get() ?? $user->primaryTpqProfile();

        if ($tpqProfile) {
            CurrentTpq::setFromProfile($tpqProfile);
            
            // Cek apakah user memiliki akses ke panel ini
            if ($user->canAccessPanel($panel)) {
                $this->redirect(url("/{$tpqProfile->slug}/admin"), navigate: false);
                return;
            }

            // User tidak memiliki akses, logout dan tampilkan form login
            Filament::auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            // Lanjut ke bawah untuk menampilkan form login
        } else {
            // Cek registrasi terakhir
            $registration = $user->tpqRegistrations()->latest()->first();

            if ($registration) {
                if ($registration->status === \App\Models\TpqRegistration::STATUS_PENDING) {
                    session(['registration_status' => $registration->status]);
                    $this->redirect(route('registration.pending'), navigate: false);
                    return;
                }

                if ($registration->tpqProfile) {
                    CurrentTpq::setFromProfile($registration->tpqProfile);

                    // Cek apakah user memiliki akses ke panel ini
                    if ($user->canAccessPanel($panel)) {
                        $this->redirect(url("/{$registration->tpqProfile->slug}/admin"), navigate: false);
                        return;
                    }

                    // User tidak memiliki akses, logout
                    Filament::auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                }
            }
        }
    }

    $this->form->fill(); // ← WAJIB ADA, ini yang bikin form bisa disubmit
}

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::auth/pages/login.actions.register.label'))
            ->url(url('/universal/admin/register'));
    }



public function authenticate(): ?LoginResponse
{
    $data = $this->form->getState();

    $authGuard = Filament::auth();
    $authProvider = $authGuard->getProvider();

    $credentials = [
        'email' => $data['email'],
        'password' => $data['password'],
    ];

    $user = $authProvider->retrieveByCredentials($credentials);

    if (! $user || ! $authProvider->validateCredentials($user, $credentials)) {
        $this->throwFailureValidationException();
    }

    if (! $user->is_active) {
        throw ValidationException::withMessages([
            'data.email' => 'Akun Anda tidak aktif. Hubungi administrator.',
        ]);
    }

    // Superadmin
    if ($user->isSuperAdmin()) {
        $authGuard->login($user, $data['remember'] ?? false);
        session()->regenerate();

        $this->redirect('/superadmin', navigate: false);

        return null;
    }

    // Ambil TPQ dari CurrentTpq (URL slug) atau dari primaryTpqProfile milik user (Login universal)
    $tpq = CurrentTpq::get() ?? $user->primaryTpqProfile();

    if (! $tpq) {
        $registration = $user->tpqRegistrations()->latest()->first();

        if ($registration && $registration->status === TpqRegistration::STATUS_PENDING) {
            $authGuard->login($user, $data['remember'] ?? false);
            session()->regenerate();

            session(['registration_status' => $registration->status]);
            $this->redirect(route('registration.pending'), navigate: false);

            return null;
        }

        throw ValidationException::withMessages([
            'data.email' => 'TPQ tidak ditemukan.',
        ]);
    }

    // Set Spatie Permission team
    app(PermissionRegistrar::class)->setPermissionsTeamId($tpq->id);
    $user->unsetRelation('roles');

    // Pastikan user memiliki role pada TPQ tersebut
    $hasAccess = $user->roles()
        ->where('model_has_roles.team_id', $tpq->id)
        ->exists();

    if (! $hasAccess) {
        throw ValidationException::withMessages([
            'data.email' => 'Anda tidak memiliki akses ke TPQ ini.',
        ]);
    }

    // Login
    $authGuard->login($user, $data['remember'] ?? false);

    session()->regenerate();

    // Simpan TPQ aktif
    CurrentTpq::setFromProfile($tpq);

    // Redirect eksplisit dengan slug TPQ
    $this->redirect(
        route('filament.admin.pages.dashboard', [
            'tpq_slug' => $tpq->slug,
        ]),
        navigate: false
    );

    return null;
}


}
