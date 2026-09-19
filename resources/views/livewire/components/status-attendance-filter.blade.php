<div class="flex items-center m-2 gap-1.5">
    <label for="filter-status" class="text-xs font-medium text-slate-600">Status:</label>
    <select wire:model.live="selectedStatus" id="filter-status" 
        class="text-xs py-1 px-1 border border-slate-200 rounded-md bg-slate-50 text-slate-800 font-medium focus:ring-teal-500 focus:border-teal-500">
        <option value="all">Semua Status</option>
        <option value="present">Hadir</option>
        <option value="late">Terlambat</option>
        <option value="sick">Sakit</option>
        <option value="permission">Izin</option>
        <option value="absent">Tanpa Keterangan</option>
    </select>
</div>