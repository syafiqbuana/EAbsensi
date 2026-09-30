<x-filament-panels::page>
    <form wire:submit="export" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-printer">
                Ekspor PDF (Queue)
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
