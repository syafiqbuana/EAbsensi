<div class="flex items-center m-2 gap-1.5">
    <label for="filter-bulan" class="font-medium text-xs text-slate-600">Bulan:</label>
    <select wire:model.live="selectedMonth" id="filter-bulan" 
        class="text-xs py-1 px-1 border border-slate-200 rounded-md bg-slate-50 text-slate-800 font-medium focus:ring-teal-500 focus:border-teal-500">
        @foreach(range(0, 5) as $i)
            @php 
                $date = now()->subMonths($i); 
            @endphp
            <option value="{{ $date->format('Y-m') }}">
                {{ $date->translatedFormat('F Y') }}
            </option>
        @endforeach
    </select>
</div>