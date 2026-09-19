<x-layouts.app>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">
                    {{ request()->route()->defaults['title'] ?? 'Riwayat Kehadiran' }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Pantau presensi harian santri, ketepatan waktu check-in, dan rekap bulanan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button href="{{ route('leaveRequest.create') }}" class="rounded-md! shadow-xs transition-colors"
                    size="sm" color="teal" variant="primary" wire:navigate>
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    Ajukan Izin / Sakit
                </flux:button>
            </div>
        </div>
        <flux:card class="!p-4 border-slate-200 shadow-xs space-y-4">
            <livewire:components.child-card-selector />
            <div
                class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs md:text-sm">
                <div class="flex flex-wrap items-center gap-3">
                    <livewire:components.month-range-filter />
                    <livewire:components.status-attendance-filter />
                </div>
            </div>
        </flux:card>
        <livewire:components.child-performance-card />
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <livewire:components.attendance-history-table />
            <div class="space-y-6">
                <livewire:components.attendance-calendar />
                <div
                    class="bg-gradient-to-br from-teal-900 to-slate-900 p-5 rounded-2xl text-white shadow-sm space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center">
                            <x-heroicon-o-information-circle class="w-5 h-5" />
                        </div>
                        <h4 class="font-bold text-sm">Kebijakan Presensi TPQ</h4>
                    </div>
                    <ul class="text-xs text-slate-200/90 space-y-2 leading-relaxed">
                        <li class="flex items-start gap-2">
                            <span class="text-teal-400 mt-0.5">•</span>
                            <span>Scan QR Mandiri dibuka 15 menit sebelum waktu halaqah dimulai.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-teal-400 mt-0.5">•</span>
                            <span>Toleransi keterlambatan maksimal 15 menit. Lebih dari batas wajib lapor ustadz
                                piket.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-teal-400 mt-0.5">•</span>
                            <span>Izin ketidakhadiran dapat diajukan secara online maksimal pukul 14:00 WIB hari
                                berjalan.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </section>
    </div>
</x-layouts.app>
