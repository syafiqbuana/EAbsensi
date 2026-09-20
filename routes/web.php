<?php

use App\Http\Middleware\ResolveTpqTenant;
use App\Livewire\RegistrationPending;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', fn() => view('welcome'));

Route::get('/login', fn() => abort(404))->name('login');

Route::middleware('auth')
    ->get('/register/pending', RegistrationPending::class)
    ->name('registration.pending');

Route::prefix('{tpq_slug}')
    ->where(['tpq_slug' => '[a-zA-Z0-9\-]+'])
    ->middleware([ResolveTpqTenant::class])
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('/login', [
                AuthenticatedSessionController::class,
                'create',
            ])->name('tenant.login');

            Route::post('/login', [
                AuthenticatedSessionController::class,
                'store',
            ])->name('tenant.login.store');
        });
        Route::middleware('auth')->group(function () {

            Route::get('/dashboard', fn() => view('dashboard.index'))
                ->name('dashboard')
                ->defaults('title', 'Dasbor');

            Route::get('/students-schedules', fn() => view('students-schedules.index'))
                ->name('studentsSchedulesIndex')
                ->defaults('title', 'Jadwal Anak');

            Route::get('/attendances-history', fn() => view('attendances-history.index'))
                ->name('attendanceHistoryIndex')
                ->defaults('title', 'Riwayat Kehadiran');

            Route::get('/leave-requests', fn() => view('leave-requests.index'))
                ->name('leaveRequest')
                ->defaults('title', 'Pengajuan Izin');

            Route::get('/leave-requests/create', fn() => view('leave-requests.create.create'))
                ->name('leaveRequest.create')
                ->defaults('title', 'Buat Pengajuan Izin');

            Route::get('/child-profiles', fn() => view('child-profiles.index'))
                ->name('childProfile')
                ->defaults('title', 'Profil Anak');
        });
    });