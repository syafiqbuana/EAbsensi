@php
    $isTenantAdmin = $audience === 'tenant-admin';

    $portalLabel = $isTenantAdmin ? 'Panel Admin TPQ' : 'Portal Orang Tua';
    $contactLabel = $isTenantAdmin ? 'Hubungi Pengelola Layanan' : 'Hubungi Admin TPQ';
    $contactNumber = $isTenantAdmin
        ? config('services.support.whatsapp')
        : $tpq->contact_number;
    $contactUrl = \App\Support\PhoneNumber::toWhatsAppUrl($contactNumber);

    $reloadUrl = $isTenantAdmin
        ? route('filament.admin.auth.login', ['tpq_slug' => $tpq->slug])
        : route('tenant.login', ['tpq_slug' => $tpq->slug]);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>Layanan Ditangguhkan - {{ $tpq->name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10 sm:px-6">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-br from-slate-50 via-white to-amber-50/50"></div>
        <div aria-hidden="true" class="pointer-events-none absolute -top-32 left-1/2 -z-10 h-96 w-96 -translate-x-1/2 rounded-full bg-amber-200/30 blur-3xl"></div>

        <header class="mb-6 flex items-center gap-3">
            @if ($tpq->logo_path)
                <img src="{{ Storage::url($tpq->logo_path) }}" alt="Logo {{ $tpq->name }}"
                    class="h-11 w-11 rounded-xl border border-slate-200 bg-white object-cover shadow-sm">
            @else
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 21h18"></path>
                        <path d="M5 21V7l7-4 7 4v14"></path>
                        <path d="M9 21v-6h6v6"></path>
                    </svg>
                </div>
            @endif

            <div>
                <p class="text-sm font-bold leading-tight text-slate-900">{{ $tpq->name }}</p>
                <p class="mt-0.5 text-xs font-medium text-slate-500">{{ $portalLabel }}</p>
            </div>
        </header>

        <main class="w-full max-w-xl">
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
                <div class="h-1.5 w-full bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500"></div>

                <div class="p-6 sm:p-8">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-200">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M10 9v6"></path>
                            <path d="M14 9v6"></path>
                        </svg>
                    </div>

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-amber-700 ring-1 ring-amber-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Layanan Ditangguhkan
                    </span>

                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-[1.75rem] sm:leading-tight">
                        {{ $isTenantAdmin ? 'Akses panel admin sedang ditangguhkan' : 'Portal TPQ sedang ditangguhkan' }}
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        @if ($isTenantAdmin)
                            Akses ke panel admin <span class="font-semibold text-slate-800">{{ $tpq->name }}</span>
                            untuk sementara ditangguhkan oleh pengelola layanan. Selama masa penangguhan, Anda belum
                            dapat mengelola data TPQ.
                        @else
                            Layanan <span class="font-semibold text-slate-800">{{ $tpq->name }}</span> untuk sementara
                            tidak dapat diakses. Tim pengelola layanan sedang meninjau status TPQ sebelum layanan
                            dapat digunakan kembali.
                        @endif
                    </p>

                    <dl class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-slate-50/70 px-4">
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Nama TPQ</dt>
                            <dd class="text-right text-sm font-semibold text-slate-900">{{ $tpq->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Nomor Induk</dt>
                            <dd class="text-right text-sm font-semibold text-slate-900">{{ $tpq->registration_number }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Status</dt>
                            <dd>
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                    Ditangguhkan
                                </span>
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/60 p-4 sm:p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">Butuh informasi lebih lanjut?</p>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    @if ($isTenantAdmin)
                                        Silakan hubungi Pengelola Layanan untuk menanyakan status penangguhan dan
                                        proses pengaktifan kembali panel admin TPQ.
                                    @else
                                        Silakan hubungi Admin TPQ untuk menanyakan status layanan dan perkiraan waktu
                                        pengaktifan kembali portal orang tua.
                                    @endif
                                </p>

                                @if ($contactUrl)
                                    <a href="{{ $contactUrl }}" target="_blank" rel="noopener noreferrer"
                                        class="mt-3 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                                        </svg>
                                        {{ $contactLabel }}
                                    </a>
                                @else
                                    <p class="mt-3 text-xs font-semibold text-amber-700">
                                        Nomor kontak belum tersedia. Silakan coba kembali beberapa saat lagi.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ $reloadUrl }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 12a9 9 0 1 1-2.64-6.36"></path>
                                <path d="M21 3v6h-6"></path>
                            </svg>
                            Muat Ulang Halaman
                        </a>
                    </div>

                    <p class="mt-5 text-xs leading-5 text-slate-500">
                        Seluruh data TPQ tetap tersimpan dengan aman. Halaman ini akan kembali normal secara otomatis
                        setelah penangguhan dicabut oleh pengelola layanan.
                    </p>
                </div>
            </section>

            <p class="mt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
            </p>
        </main>
    </div>
</body>
</html>
