<?php

namespace App\Livewire\Components;

use Carbon\Carbon;
use Livewire\Component;

class AttendanceTodayWidget extends Component
{
    public array $studentsData = [];

    public function mount()
    {
        // 1. Ambil user beserta siswa dan kelasnya.
        // 2. Load attendance HANYA untuk hari ini beserta jadwalnya.
        $user = auth()->user()->load([
            'students.classes',
            'students.attendances' => function ($query) {
                $query->whereDate('date', Carbon::today())->with('schedule');
            }
        ]);
        
        $students = $user->students;

        // 3. Looping setiap siswa milik user tersebut
        foreach ($students as $student) {
            $class = $student->classes; // Asumsi relasi belongsTo sudah benar di model

            // Ambil absensi hari ini (karena query di-load khusus hari ini, kita bisa ambil yang pertama)
            $todayAttendance = $student->attendances->first();

            // Data default jika tidak ada kelas / belum ada generate absen hari ini
            $studentInfo = [
                'name'              => $student->name,
                'class_name'        => $class ? $class->name : 'Belum ada kelas',
                'schedule_name'     => 'Tidak ada jadwal',
                'attendance_status' => 'Belum ada sesi',
            ];

            // 4. Jika record absensi hari ini ditemukan dan memiliki jadwal yang terikat
            if ($todayAttendance && $todayAttendance->schedule) {
                $schedule = $todayAttendance->schedule;
                
                $studentInfo['schedule_name']     = $schedule->name;
                $studentInfo['attendance_status'] = $this->resolveAttendanceStatus($todayAttendance, $schedule);
            }

            // 5. Masukkan data siswa ini ke dalam array utama
            $this->studentsData[] = $studentInfo;
        }
    }

    /**
     * Resolving status menggunakan record attendance dan schedule yang di-passing.
     */
    private function resolveAttendanceStatus($attendance, $schedule): string
    {
        $now      = Carbon::now();
        $timeOpen = Carbon::today()->setTimeFromTimeString($schedule->time_open);
        
        // Jika jadwalnya belum saatnya dibuka
        if ($now->lt($timeOpen)) {
            return 'Belum Dibuka';
        }

        // Return status sesuai enum di database
        return match ($attendance->status) {
            'pending'    => 'Menunggu Absensi',
            'present'    => 'Hadir',
            'sick'       => 'Sakit',
            'permission' => 'Izin',
            'absent'     => 'Alfa',
            'holiday'    => 'Hari Libur',
            default      => $attendance->status,
        };
    }

    public function render()
    {
        return view('livewire.components.attendance-today-widget');
    }
}