<x-layouts.app>
    <div class="flex flex-col gap-3 lg:gap-4">
        <h1 class="text-black text-2xl font-semibold ">{{ request()->route()->defaults['title'] }}</h1>
        <livewire:components.child-card-selector />
        <livewire:components.child-profile-card />
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
            <livewire:components.qr-code-card />
        </div>
    </div>
</x-layouts.app>
