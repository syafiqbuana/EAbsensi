<x-layouts.app>
    <div class="flex flex-col gap-2 lg:gap-3">
        <h1 class="text-black text-2xl font-semibold ">{{ request()->route()->defaults['title'] }}</h1>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item wire:navigate href="{{ route('leaveRequest') }}">Permohonan Izin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Buat</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <livewire:components.leave-request-form/>
    </div>
</x-layouts.app>
