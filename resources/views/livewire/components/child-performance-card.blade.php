<div class="gap-3 flex flex-col relative relative overflow-hidden">
    <div wire:loading wire:target="loadStudentStats"
        class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center backdrop-blur-sm">
        <x-heroicon-o-arrow-path class="w-8 h-8 animate-spin text-teal-500" />
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-3 ">
        <flux:card class="p-3 flex flex-col gap-1 h-[120px] bg-green-50 border border-green-200! rounded-2xl">
            <flux:heading level="2" class="text-green-700 font-semibold" size="md">Rasio Kehadiran</flux:heading>
            <span class="font-bold text-[24px]">{{ $attendanceStats['ratio'] }} %</span>
        </flux:card>

        <flux:card class="p-3 flex flex-col h-[120px] gap-1 bg-lime-50 border border-lime-200! rounded-2xl">
            <flux:heading level="2" size="md" class="text-lime-700 font-semibold">Total Hadir</flux:heading>
            <span class="font-bold text-[24px]">{{ $attendanceStats['present'] }} Hari</span>
        </flux:card>

        <flux:card class="p-3 flex flex-col h-[120px] gap-1 bg-teal-50 border border-teal-200! rounded-2xl">
            <flux:heading level="2" size="md" class="text-teal-700 font-semibold">Izin/Sakit</flux:heading>
            <span class="font-bold text-[24px]">{{ $attendanceStats['leave'] }} Hari</span>
        </flux:card>

        <flux:card class="p-3 flex flex-col h-[120px] gap-1 bg-yellow-50 border-yellow-200! border rounded-2xl">
            <flux:heading level="2" size="md" class="text-yellow-700 font-semibold">Tanpa Keterangan</flux:heading>
            <span class="font-bold text-[24px]">{{ $attendanceStats['absent'] }} Hari</span>
        </flux:card>
    </div>
</div>
