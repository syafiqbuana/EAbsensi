<div class="flex flex-col gap-4">
    <!-- Tombol Action (Selalu tampil di atas daftar) -->
    <div class="flex justify-end mt-2">
        <!-- Gunakan wire:navigate agar transisi halaman cepat seperti SPA -->
        <flux:button href="{{ route('leaveRequest.create') }}" wire:navigate variant="primary" size="sm">
            Buat Pengajuan
        </flux:button>
    </div>

    <!-- Container Grid untuk Card -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        
        @forelse ($leaveRequests as $request)
            <!-- Card Data Izin -->
            <flux:card>
                <!-- Bagian atas card (Jenis Izin) -->
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-zinc-100">
                    <span class="text-lg font-bold text-zinc-800 capitalize">
                        {{ $request->type === 'sick' ? 'Sakit' : 'Izin' }}
                    </span>
                </div>

                <!-- Detail Poin 2, 3, 4 -->
                <div class="flex flex-col gap-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Tanggal</span>
                        <span class="font-medium text-zinc-900">{{ $request->date->format('d M Y') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Nama Murid</span>
                        <span class="font-medium text-zinc-900">{{ $request->student->name }}</span>
                    </div>

                    <div class="flex justify-between items-center pt-1">
                        <span class="text-zinc-500">Status</span>
                        @if($request->status === 'pending')
                            <flux:badge color="blue">Menunggu</flux:badge>
                        @elseif($request->status === 'approved')
                            <flux:badge color="green">Disetujui</flux:badge>
                        @else
                            <flux:badge color="red">Ditolak</flux:badge>
                        @endif
                    </div>
                </div>
            </flux:card>

        @empty
            <!-- Empty State dari Anda -->
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 flex flex-col gap-4 mt-2">
                <flux:card class="flex flex-col items-center justify-center gap-3 p-8 text-center text-zinc-500">
                    <flux:icon.hand-raised class="w-8 h-8 opacity-50" />
                    <flux:text>Belum ada permohonan Izin yang Anda ajukan.</flux:text>
                </flux:card>
            </div>
        @endforelse

    </div>
</div>