<div class="grid grid-cols-2 lg:grid-cols-4 gap-2 rounded-[16px]">
    <flux:card class="p-3 flex flex-col gap-1  bg-teal-50 border border-teal-200! border">
        <div class="w-10 h-10 bg-teal-100 border border-teal-200 rounded-full px-2 py-2">
            <x-heroicon-o-users class="w-6 h-6 text-teal-600" />
        </div>
        <flux:heading level="2" size="lg">Jumlah Anak</flux:heading>
        <span class="font-bold text-[18px]">{{ $students }} Anak</span>
        <flux:link href="/students-schedules" class="inline-flex items-center text-sm underline">
            <span>Detail</span>
        </flux:link>
    </flux:card>

    <flux:card class="p-3 flex flex-col gap-1 bg-lime-50 border border-lime-200!">
        <div class="w-10 h-10 bg-lime-100 border border-lime-200 rounded-full px-2 py-2">
            <x-heroicon-o-calendar class="w-6 h-6 text-lime-600" />
        </div>
        <flux:heading level="2" size="lg">Jumlah Jadwal</flux:heading>
        <span class="font-bold text-[18px]">{{ $schedules }} Jadwal</span>
        <flux:link href="/students-schedules" class="inline-flex items-center gap-1 text-sm underline">
            <span>Detail</span>
        </flux:link>
    </flux:card>
    <flux:card class="p-3 flex flex-col gap-1  bg-purple-50 border border-purple-200!">
        <div class="w-10 h-10 bg-purple-100 border border-purple-200 rounded-full px-2 py-2">
            <x-heroicon-o-calendar class="w-6 h-6 text-purple-600" />
        </div>
        <flux:heading level="2" size="lg">Rasio Kehadiran</flux:heading>
        <span class="font-bold text-[18px]">80% Kehadiran</span>
        <flux:link href="/students-schedules" class="inline-flex items-center gap-1 text-sm underline">
            <span>Detail</span>
        </flux:link>
    </flux:card>
    <flux:card class="p-3 flex flex-col gap-1  bg-orange-50 border border-orange-200!">
        <div class="w-10 h-10 bg-orange-100 rounded-full  px-2 py-2 border border-orange-200">
            <x-heroicon-o-calendar class="w-6 h-6 text-orange-600" />
        </div>

        <flux:heading level="2" size="lg">Tidak Hadir</flux:heading>
        <span class="font-bold text-[18px]">{{ $totalAbsence }} Hari</span>
        <flux:link href="/students-schedules" class="inline-flex items-center gap-1 text-sm underline">
            <span>Detail</span>
        </flux:link>
    </flux:card>
</div>
