
    <flux:header
        class="lg:hidden flex! flex-row! justify-between! sticky top-0 z-50 bg-white dark:bg-zinc-800 border-b gap-3 border-zinc-200 dark:border-zinc-700">
        <div class="flex flex-row gap-2 items-center">
            <flux:sidebar.toggle class="lg:hidden!" icon="bars-3" class="text-black " inset="left" />
            <span class="font-semibold text-lg">{{ App\Support\CurrentTpq::name() }}</span>
        </div>
        <livewire:components.datetime />
    </flux:header>
