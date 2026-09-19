<div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden relative">
    
    {{-- Loading Overlay (Hanya muncul saat request ke server berlangsung) --}}
    <div wire:loading 
         class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center">
        <svg class="w-8 h-8 text-teal-600 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- Table Header / Title -->
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-bold text-slate-900 text-base">Jurnal Presensi Harian</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rekap check-in QR santri oleh ustadz pengajar.</p>
        </div>
        
        <!-- Quick Search Input -->
        <div class="relative w-full sm:w-64">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input wire:model.live.debounce.400ms="search" type="text" 
                class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-teal-500 focus:border-teal-500" 
                placeholder="Cari ustadz / catatan..."/>
        </div>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs md:text-sm">
            <thead>
                <tr class="bg-slate-50/75 border-b border-slate-200/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <th class="py-3 px-4">Tanggal & Sesi</th>
                    <th class="py-3 px-4">Check-In</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-normal text-slate-700">
                @forelse ($attendances as $attendance)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        
                        <!-- Kolom Tanggal -->
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-900">
                                {{-- Menggunakan field 'date' dari model yang sudah di-cast --}}
                                {{ $attendance->date ? $attendance->date->translatedFormat('l, d M Y') : $attendance->created_at->translatedFormat('l, d M Y') }}
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ $attendance->schedule?->name ?? 'Sesi Reguler' }}
                            </div>
                        </td>

                        <!-- Kolom Waktu Check-In -->
                        <td class="py-3.5 px-4">
                            @if($attendance->status === \App\Models\Attendance::STATUS_PRESENT)
                                <div class="font-mono font-semibold text-slate-800">
                                    {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }} WIB
                                </div>
                            @else
                                <div class="text-slate-400 font-mono">-</div>
                                <div class="text-[11px] text-slate-400">Tidak Scan</div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            @php
                                $statusStyle = match($attendance->status) {
                                    \App\Models\Attendance::STATUS_PRESENT => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'bg-emerald-500', 'Hadir'],
                                    \App\Models\Attendance::STATUS_PERMISSION => ['bg-blue-50', 'text-blue-700', 'border-blue-200', 'bg-blue-500', 'Izin'],
                                    \App\Models\Attendance::STATUS_SICK => ['bg-violet-50', 'text-violet-700', 'border-violet-200', 'bg-violet-500', 'Sakit'],
                                    \App\Models\Attendance::STATUS_ABSENT => ['bg-red-50', 'text-red-700', 'border-red-200', 'bg-red-500', 'Tanpa Keterangan'],
                                    \App\Models\Attendance::STATUS_HOLIDAY => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'bg-amber-500', 'Libur'],
                                    \App\Models\Attendance::STATUS_PENDING => ['bg-slate-100', 'text-slate-600', 'border-slate-300', 'bg-slate-400', 'Menunggu'],
                                    default => ['bg-slate-50', 'text-slate-700', 'border-slate-200', 'bg-slate-500', 'Tidak Diketahui'],
                                };
                            @endphp               
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusStyle[0] }} {{ $statusStyle[1] }} border {{ $statusStyle[2] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusStyle[3] }}"></span>
                                {{ $statusStyle[4] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm font-medium">Tidak ada riwayat kehadiran ditemukan.</p>
                                <p class="text-xs">Coba sesuaikan filter bulan atau status pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 bg-white">
        {{ $attendances->links('pagination::tailwind') }}
    </div>

</div>