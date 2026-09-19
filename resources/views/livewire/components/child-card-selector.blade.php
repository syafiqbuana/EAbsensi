<div x-data="{ selectedStudentId: {{ $students->first()?->id ?? 'null' }} }">
    <flux:card class="!p-2 border-none!">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2">

            <div class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar gap-2 pb-2 sm:pb-0">

                @foreach ($students as $student)
                    <button type="button"
                        @click="
                            selectedStudentId = {{ $student->id }};
                            $wire.dispatch('studentSelected', {
                                studentId: {{ $student->id }}
                            });
                        "
                        :class="selectedStudentId === {{ $student->id }} ?
                            'bg-teal-50/80 border border-teal-500 shadow-sm' :
                            'bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200'"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-xl text-left transition-all shrink-0 w-[85vw] sm:w-auto snap-center">

                        {{-- Avatar --}}
                        @if ($student->photo_path)
                            <img src="{{ Storage::url($student->photo_path) }}" alt="{{ $student->name }}"
                                class="w-10 h-10 rounded-full object-cover ring-2 ring-white">
                        @else
                            <div :class="selectedStudentId === {{ $student->id }} ?
                                'bg-teal-600' :
                                'bg-slate-400 group-hover:bg-slate-500'"
                                class="w-10 h-10 rounded-full text-white font-bold flex items-center justify-center text-sm ring-2 ring-white transition-colors">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="min-w-0 pr-4 sm:pr-0">
                            <div class="flex items-center gap-2">
                                <span
                                    :class="selectedStudentId === {{ $student->id }} ?
                                        'text-slate-900' :
                                        'text-slate-700 group-hover:text-slate-900'"
                                    class="text-sm font-bold truncate">
                                    {{ $student->name }}
                                </span>
                            </div>

                            <span
                                :class="selectedStudentId === {{ $student->id }} ?
                                    'text-teal-800' :
                                    'text-slate-500'"
                                class="text-xs font-medium truncate block mt-0.5">
                                Kelas {{ $student->classes?->name ?? 'Belum ada kelas' }}
                            </span>
                        </div>

                    </button>
                @endforeach

            </div>
        </div>
    </flux:card>
</div>
