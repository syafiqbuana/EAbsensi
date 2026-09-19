<flux:card class="!p-5 border-slate-200 shadow-xs relative overflow-hidden">
    
    {{-- Loading State Khusus Kalender --}}
    <div wire:loading wire:target="loadCalendarData" 
         class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
        <x-heroicon-o-arrow-path class="w-6 h-6 animate-spin text-teal-500" />
    </div>

    <!-- Header Kalender -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-slate-900 text-sm">Kalender Presensi</h3>
        <span class="text-xs font-semibold text-slate-500">{{ $currentMonthName }}</span>
    </div>

    <!-- Grid Nama Hari -->
    <div class="grid grid-cols-7 gap-1.5 text-center text-xs font-medium text-slate-400 mb-2">
        <div>Sn</div><div>Sl</div><div>Rb</div><div>Km</div><div>Jm</div><div>Sb</div><div>Mg</div>
    </div>

    <!-- Grid Tanggal -->
    <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
        
        {{-- Empty offset untuk hari sebelum tanggal 1 --}}
        @for ($i = 0; $i < $firstDayOffset; $i++)
            <div></div>
        @endfor

        {{-- Loop Tanggal 1 sampai akhir bulan --}}
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateString = $yearMonthPrefix . str_pad($day, 2, '0', STR_PAD_LEFT);
                $status = $attendanceMap[$dateString] ?? null;
                $isToday = $dateString === now()->format('Y-m-d');
                
                // Menentukan Style berdasarkan Status
                $dayStyle = match($status) {
                    \App\Models\Attendance::STATUS_PRESENT => 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200',
                    \App\Models\Attendance::STATUS_PERMISSION => 'bg-blue-50 text-blue-800 font-bold border border-blue-200',
                    \App\Models\Attendance::STATUS_SICK => 'bg-violet-50 text-violet-800 font-bold border border-violet-200',
                    \App\Models\Attendance::STATUS_ABSENT => 'bg-red-50 text-red-800 font-bold border border-red-300', // Alfa = Danger (Red solid)
                    \App\Models\Attendance::STATUS_HOLIDAY => 'bg-rose-50 text-rose-800 font-bold border border-rose-200', // Libur = Merah (Rose soft)
                    \App\Models\Attendance::STATUS_PENDING => 'bg-slate-100 text-slate-600 font-bold border border-slate-300',
                    default => 'bg-slate-50 text-slate-400', // Belum ada presensi (Null)
                };

                // Highlight jika hari ini
                if ($isToday && !$status) {
                    $dayStyle = 'bg-teal-50 text-teal-700 font-bold ring-2 ring-teal-500/30';
                } elseif ($isToday && $status) {
                    $dayStyle .= ' ring-2 ring-teal-500/50';
                }
            @endphp

            <div class="p-1.5 rounded-lg {{ $dayStyle }}" title="{{ $status ? ucfirst($status) : 'Belum ada data' }}">
                {{ $day }}
            </div>
        @endfor
    </div>

    <!-- Legend Dots -->
    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-center sm:justify-between gap-3 text-[11px] text-slate-600">
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>Hadir</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span><span>Izin</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-violet-500"></span><span>Sakit</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-red-600"></span><span>Alfa</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-rose-400"></span><span>Libur</span>
        </div>
    </div>
</flux:card>