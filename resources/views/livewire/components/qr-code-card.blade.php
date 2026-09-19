{{-- resources/views/livewire/components/child-qr-card.blade.php --}}
<div class="h-full">
    @if($student)
        <flux:card class="!p-6 flex h-full flex-col justify-between shadow-sm border-slate-200 relative overflow-hidden">
            
            {{-- Bagian Header Kartu --}}
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center shrink-0">
                        <x-heroicon-o-qr-code class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Kartu QR Presensi</h3>
                        <p class="text-[11px] text-slate-400">Scan kehadiran harian di TPQ</p>
                    </div>
                </div>
            </div>

            {{-- Bagian QR Visual --}}
            <div class="my-6 flex flex-col items-center">
                {{-- Bingkai Dashed Teal untuk QR Code --}}
                <div class="p-3 bg-white border-2 border-dashed border-teal-300 rounded-2xl shadow-sm inline-block">
                    {{-- Memanggil gambar asli dari accessor Model: $student->qr_code --}}
                    <img src="{{ $student->qr_code }}" alt="QR Code {{ $student->name }}" class="w-44 h-44 object-contain rounded-lg">
                </div>
                
                {{-- Teks Token di bawah QR --}}
                <div class="text-center mt-4">
                    <span class="inline-block text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200 tracking-wider">
                        {{ strtoupper($student->name) }}
                    </span>
                    <p class="text-[11px] text-slate-500 mt-2">Presensi hanya dilakukan oleh Guru.</p>
                </div>
            </div>

            {{-- Tombol Aksi Bawah menggunakan Flux Button --}}
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 mt-auto">
                <flux:button variant="outline" class="w-full text-xs text-slate-700 hover:bg-slate-50" icon="arrow-down-tray">
                    Unduh Kartu
                </flux:button>
                
                {{-- Perhatikan penggunaan warna solid custom via class atau properti color flux --}}
                <flux:button class="w-full text-xs bg-teal-600 hover:bg-teal-700 text-white border-none" icon="arrows-pointing-out">
                    Perbesar
                </flux:button>
            </div>
            
        </flux:card>
    @else
        {{-- State Kosong jika tidak ada santri yang dipilih --}}
        <flux:card class="flex h-full min-h-[350px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 text-center">
            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-200 text-slate-400">
                <x-heroicon-o-qr-code class="w-6 h-6" />
            </div>
            <p class="text-sm font-medium text-slate-500">Pilih data anak untuk melihat QR Code.</p>
        </flux:card>
    @endif
</div>