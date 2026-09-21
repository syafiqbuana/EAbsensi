<x-layouts.app>
    <div class="flex flex-col gap-3 lg:gap-4">
        <h1 class="text-black text-2xl font-semibold ">{{ request()->route()->defaults['title'] }}</h1>
    </div>
</x-layouts.app>
