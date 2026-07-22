<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard')->defaults('title', 'Dasbor');

    Route::get('/students-schedules', function () {
        return view('students-schedules.index');
    })->defaults('title', 'Jadwal Anak')->name('studentsIndex');

    Route::get('/leave-requests', function () {
        return view('leave-requests.index');
    })->defaults('title', 'Pengajuan Izin')
        ->name('leaveRequest');

    Route::get('/leave-requests/create', function () {
        return view('leave-requests.create.create');
    })->defaults('title', 'Buat Pengajuan Izin')
        ->name('leaveRequest.create');
});
