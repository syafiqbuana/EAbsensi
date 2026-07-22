<div class="flex flex-col gap-6 mt-6">

    <form wire:submit="save">
        <flux:card class="flex flex-col gap-6">

            <flux:field>
                <flux:label>Pilih Murid</flux:label>
                <flux:select wire:model="student_id" placeholder="Pilih nama anak...">
                    @foreach ($students as $student)
                        <flux:select.option value="{{ $student->id }}">{{ $student->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="student_id" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Tipe Pengajuan</flux:label>
                    <flux:select wire:model="type" placeholder="Pilih jenis..." class="text-black">
                        <flux:select.option value="sick">Sakit</flux:select.option>
                        <flux:select.option value="permission">Izin</flux:select.option>
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Ketidakhadiran</flux:label>
                    <flux:input type="date" wire:model="date"   min="{{ now()->toDateString() }}"/>
                    <flux:error name="date" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Alasan / Keterangan</flux:label>
                <flux:textarea wire:model="description" rows="3"
                    placeholder="Tuliskan deskripsi ketidakhadiran di sini..." />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>Lampiran Bukti (Opsional)</flux:label>
                <flux:input type="file" size="sm" wire:model="proof" accept="image/*" />
                <flux:error name="proof" />

                @if ($proof)
                    <div class="mt-4">
                        <span class="block text-xs font-medium text-zinc-500 mb-2">Preview Bukti:</span>
                        <div class="relative w-32 h-32 rounded-lg overflow-hidden border border-zinc-200 shadow-sm">
                            <img src="{{ $proof->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                        </div>
                    </div>
                @endif
            </flux:field>

            <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-zinc-100">
                <flux:button href="{{ route('leaveRequest') }}" size="sm" wire:navigate variant="ghost">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Kirim Permohonan</span>
                    <span wire:loading wire:target="save">Sedang Memproses...</span>
                </flux:button>
            </div>

        </flux:card>
    </form>
</div>
