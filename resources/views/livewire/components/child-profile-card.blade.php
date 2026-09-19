<div>
    @if($student)
        <flux:card class="!p-6 lg:!p-7 shadow-sm border-slate-200">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div class="flex min-w-0 w-full flex-col sm:flex-row items-center sm:items-start gap-5">
                    <div class="relative shrink-0">
                        <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-gradient-to-tr from-teal-700 via-teal-600 to-emerald-400 p-1 shadow-md">
                            <div class="w-full h-full rounded-[14px] bg-slate-100 flex items-center justify-center overflow-hidden border-2 border-white">
                                @if ($student->photo_path)
                                    <img
                                        src="{{ Storage::url($student->photo_path) }}"
                                        alt="{{ $student->name }}"
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <div class="flex flex-col items-center justify-center text-teal-800 bg-slate-100 h-full w-full">
                                        <span class="text-4xl font-extrabold">
                                            {{ strtoupper(substr($student->name, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 w-full text-center sm:text-left space-y-2">
                        <div class="flex min-w-0 flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h2 class="min-w-0 max-w-full text-2xl font-bold text-slate-900 tracking-tight break-words">
                                {{ $student->name }}
                            </h2>
                        </div>
                        <p class="text-sm font-medium text-slate-600 flex items-center justify-center sm:justify-start gap-2">
                            <span class="inline-flex items-center text-teal-700 font-semibold">
                                <svg
                                    class="w-4 h-4 mr-1 shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                    />
                                </svg>
                                <span class="break-words">
                                    Kelas {{ $student->classes?->name ?? 'Belum ada kelas' }}
                                </span>
                            </span>
                        </p>
                        <div class="pt-3 grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-4 text-xs text-slate-600 text-left">
                            <div class="min-w-0">
                                <span class="text-slate-400 block font-medium">
                                    Usia Terhitung:
                                </span>
                                <span class="font-bold text-slate-800 break-words">
                                    {{ $student->count_age ?? '-' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-slate-400 block font-medium">
                                    Tempat, Tgl Lahir:
                                </span>
                                <span class="font-bold text-slate-800 break-words">
                                    {{ $student->birth_place ?? '-' }},
                                    {{ $student->birth_date ? $student->birth_date->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-slate-400 block font-medium">
                                    Jenis Kelamin:
                                </span>
                                <span class="font-bold text-slate-800 break-words">
                                    {{
                                        in_array(
                                            strtolower($student->gender),
                                            ['l', 'male', 'laki-laki']
                                        )
                                            ? 'Laki-laki'
                                            : 'Perempuan'
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row md:flex-col items-center justify-end gap-2 shrink-0 w-full md:w-auto mt-4 md:mt-0">
                    <flux:button
                        variant="filled"
                        color="zinc"
                        class="w-full md:w-auto text-xs"
                        icon="pencil"
                    >
                        Perbarui Data Anak
                    </flux:button>
                </div>
            </div>
        </flux:card>
    @else
        <flux:card class="flex h-64 flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 text-center">
            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-200">
                <svg
                    class="h-6 w-6 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-500">
                Belum ada data anak yang dipilih.
            </p>
        </flux:card>
    @endif
</div>