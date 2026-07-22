<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <title class="hidden">{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="min-h-screen bg-white">

    <livewire:components.app-header />
    <flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
        <flux:navlist variant="outline">
            <flux:navlist.item icon="home" href="/dashboard" wire:navigate>
                Dasbor
            </flux:navlist.item>

            <flux:spacer />

            <flux:navlist.item icon="calendar-days" href="/students-schedules" wire:navigate>
                Jadwal Anak
            </flux:navlist.item>

            <flux:navlist.item icon="clipboard-document-list" href="/absen" wire:navigate>
                Riwayat Absensi
            </flux:navlist.item>

            <flux:navlist.item icon="hand-raised" href="/leave-requests" wire:navigate>
                Pengajuan Izin
            </flux:navlist.item>
            <flux:navlist.item icon="user-group" href="/izin" wire:navigate>
                Profil Anak
            </flux:navlist.item>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="user" href="/profil" wire:navigate>
                Profil
            </flux:navlist.item>
            <flux:modal.trigger name="logout-modal">
                <flux:navlist.item icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                    Logout
                </flux:navlist.item>
            </flux:modal.trigger>
        </flux:navlist>
    </flux:sidebar>

    <flux:main class="p-0 m-0">
        {{ $slot }}
    </flux:main>

    <flux:modal name="logout-modal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Logout</flux:heading>
                <flux:subheading>Apakah Anda yakin ingin keluar?</flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>

                {{-- Form submit diletakkan di dalam modal --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button type="submit" variant="danger">
                        Ya, Logout
                    </flux:button>
                </form>
            </div>
        </div>
    </flux:modal>

    @livewireScripts
    @fluxScripts
        <flux:toast position="top end" />
</body>

</html>
