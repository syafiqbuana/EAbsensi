<flux:card class="w-full flex flex-col rounded-2xl p-0 m-0 overflow-hidden shadow-sm">

    <div class="w-full border-b border-slate-200 p-4">
        <div class="flex items-center gap-2">
            <span class="text-[16px] text-black">Jadwal Berikutnya</span>
        </div>
    </div>

    
    @if (empty($upcomingSchedules))
        {{-- Header untuk Empty State --}}
        {{-- Empty State Content --}}
        <div class="p-4 flex justify-center items-center">
            <div class="flex w-full min-h-[200px] flex-col items-center justify-center text-center bg-zinc-50 py-6 px-4 rounded-xl border border-zinc-200 gap-3">
                <x-heroicon-o-calendar-date-range class="h-12 w-12 text-zinc-400 bg-white rounded-full border border-zinc-200 p-2.5 shadow-sm"/>
                <div>
                    <p class="text-sm font-semibold text-zinc-900">Tidak Ada Jadwal</p>
                    <p class="text-sm text-zinc-500 mt-1">Silakan kembali besok atau cek menu jadwal anak untuk melihat jadwal lengkap.</p>
                </div>
                <flux:button wire:navigate href="/students-schedules" variant="primary" size="sm" class="mt-2">
                    Jadwal Anak
                </flux:button>
            </div>
        </div>
    @else
        {{-- Alpine Component Wrap (Membungkus Header & Slider) --}}
        <div x-data="{
            current: 0,
            total: {{ count($upcomingSchedules) }},
            timer: null,
            touchStartX: 0,
        
            init() {
                if (this.total > 1) this.startAuto();
            },
            startAuto() {
                this.timer = setInterval(() => this.next(), 4000);
            },
            stopAuto() {
                clearInterval(this.timer);
            },
            prev() {
                this.stopAuto();
                this.current = (this.current - 1 + this.total) % this.total;
                this.startAuto();
            },
            next() {
                this.current = (this.current + 1) % this.total;
            },
            goTo(i) {
                this.stopAuto();
                this.current = i;
                this.startAuto();
            },
            onTouchStart(e) {
                this.touchStartX = e.changedTouches[0].screenX;
            },
            onTouchEnd(e) {
                const diff = this.touchStartX - e.changedTouches[0].screenX;
                if (Math.abs(diff) > 50) {
                    diff > 0 ? this.next() : this.prev();
                }
            },
        }" x-init="init()" class="flex flex-col w-full">
            
            {{-- Header Terintegrasi --}}      
                {{-- Dots Navigation dipindah ke kanan header --}}
                @if (count($upcomingSchedules) > 1)
                    <div class="flex items-center gap-1.5">
                        @foreach ($upcomingSchedules as $i => $_)
                            <button @click="goTo({{ $i }})"
                                :class="current === {{ $i }} ? 'w-4 bg-yellow-500' : 'w-2 bg-slate-200 hover:bg-slate-300'"
                                class="h-2 rounded-full transition-all duration-300 focus:outline-none"></button>
                        @endforeach
                    </div>
                @endif

            {{-- Slides Container --}}
            <div class="relative grid overflow-hidden" @touchstart="onTouchStart($event)" @touchend="onTouchEnd($event)">
                @foreach ($upcomingSchedules as $i => $schedule)
                    {{-- Ubah padding disini agar rapi tanpa nested card --}}
                    <div class="col-start-1 row-start-1 p-5 w-full bg-white" 
                        x-show="current === {{ $i }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-8"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-8">
                        
                        {{-- Hapus <flux:card> disini, ganti flex biasa --}}
                        <div class="flex flex-col gap-4">
                            <h3 class="text-base font-bold text-slate-800">{{ $schedule['name'] }}</h3>

                            <div class="flex flex-col gap-3">
                                <div class="flex items-start gap-3">
                                    <x-heroicon-o-calendar class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" />
                                    <span class="text-[15px] text-slate-600 font-medium">
                                        {{ $schedule['next_day'] }}, {{ $schedule['next_date'] }}
                                    </span>
                                </div>
                                <div class="flex items-start gap-3">
                                    <x-heroicon-o-clock class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" />
                                    <span class="text-[15px] text-slate-600">
                                        {{ $schedule['time_open'] }} – {{ $schedule['time_close'] }} WIB
                                    </span>
                                </div>
                                <div class="flex items-start gap-3">
                                    <x-heroicon-o-user class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" />
                                    <span class="text-[15px] text-slate-600">
                                        {{ $schedule['student_names'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Tombol prev/next manual dibuat lebih subtle (opsional, karena sudah ada swipe & dots) --}}
                @if (count($upcomingSchedules) > 1)
                    <button @click="prev()"
                        class="absolute left-1 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white shadow-sm border border-slate-100 rounded-full p-1.5 transition z-10 text-slate-400 hover:text-slate-600 opacity-0 md:opacity-100">
                        <x-heroicon-o-chevron-left class="w-4 h-4" />
                    </button>
                    <button @click="next()"
                        class="absolute right-1 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white shadow-sm border border-slate-100 rounded-full p-1.5 transition z-10 text-slate-400 hover:text-slate-600 opacity-0 md:opacity-100">
                        <x-heroicon-o-chevron-right class="w-4 h-4" />
                    </button>
                @endif
            </div>
            
        </div>
    @endif

</flux:card>