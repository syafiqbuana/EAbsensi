<flux:header
    class="lg:hidden flex! flex-row! items-center! justify-between! sticky top-0 z-50 bg-white dark:bg-zinc-800 border-b gap-2 border-zinc-200 dark:border-zinc-700">
    {{-- Left --}}
    <div class="flex flex-row items-center gap-2 min-w-0 flex-1">
        <flux:sidebar.toggle class="lg:hidden! text-black shrink-0" icon="bars-3" inset="left" />

        @php
            $tpq = App\Support\CurrentTpq::get();
        @endphp

        @if ($tpq?->logo_path)
            <img src="{{ Storage::url($tpq->logo_path) }}" alt="{{ $tpq->name }}"
                class="hidden lg:block size-8 shrink-0 object-cover border border-zinc-200 dark:border-zinc-700">
        @endif
        <span class="font-semibold text-md truncate min-w-0">
            {{ $tpq?->name }}
        </span>
    </div>

    {{-- Right --}}
    <div class="shrink-0">
        <livewire:components.datetime />
    </div>
</flux:header>
