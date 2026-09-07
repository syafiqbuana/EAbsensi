<style>
        .animate-pulse-ring {
            animation: pulse-ring 2.5s ease-in-out infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.35); }
            50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
        }
        .animate-modal-in {
            animation: modal-in 0.2s ease-out;
        }
        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.95) translateY(6px); }
            to { opacity: 1; transform: none; }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans text-gray-800 antialiased">
    
    {{-- 👇 INI ADALAH WRAPPER TUNGGAL (SINGLE ROOT ELEMENT) UNTUK LIVEWIRE 👇 --}}
    <main class="flex min-h-screen flex-col items-center justify-center p-4 md:p-8">
        
        {{-- Livewire Polling Container --}}
        <div wire:poll.5s="checkStatus" class="w-full max-w-[700px]">

            {{-- Logo & Brand --}}
            <div class="mb-8 text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 no-underline">
                    <div class="flex h-[42px] w-[42px] items-center justify-center rounded-xl bg-indigo-600 text-[1.1rem] font-extrabold tracking-tighter text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span class="text-[1.2rem] font-bold tracking-tight text-gray-900">Sistem<span class="text-indigo-600">TPQ</span></span>
                </a>
                <p class="mt-2 text-[0.8rem] tracking-wide text-gray-500">Sistem Informasi Manajemen TPQ</p>
            </div>

            {{-- Status Card --}}
            <div class="relative mb-4 overflow-hidden rounded-2xl border border-gray-200 bg-white p-7 text-center shadow-sm">
                @if ($status === 'pending')
                    <div class="absolute left-0 top-0 h-1 w-full bg-amber-400"></div>
                @elseif ($status === 'approved')
                    <div class="absolute left-0 top-0 h-1 w-full bg-emerald-500"></div>
                @else
                    <div class="absolute left-0 top-0 h-1 w-full bg-red-500"></div>
                @endif

                <div class="{{ $status === 'pending' ? 'bg-amber-50 text-amber-500 animate-pulse-ring' : ($status === 'approved' ? 'bg-emerald-50 text-emerald-500' : 'bg-red-50 text-red-500') }} mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full text-[2rem]">
                    @if ($status === 'pending')
                        ⏳
                    @elseif ($status === 'approved')
                        ✅
                    @else
                        ❌
                    @endif
                </div>
                
                <h2 class="mb-2 text-xl font-bold text-gray-900">
                    @if ($status === 'pending')
                        Menunggu Persetujuan
                    @elseif ($status === 'approved')
                        Pendaftaran Disetujui!
                    @else
                        Pendaftaran Ditolak
                    @endif
                </h2>
                
                <p class="text-sm leading-relaxed text-gray-600">
                    @if ($status === 'pending')
                        Pendaftaran TPQ Anda sedang ditinjau oleh administrator sistem.<br>
                        Halaman ini otomatis diperbarui secara berkala.
                    @elseif ($status === 'approved')
                        Selamat! Pendaftaran TPQ Anda telah disetujui. Klik tombol di bawah untuk masuk ke panel admin Anda.
                    @else
                        Mohon maaf, pendaftaran Anda tidak dapat diproses. Silakan lihat alasan penolakan di bawah ini.
                    @endif
                </p>

                @if ($status === 'approved')
                    <a href="{{ url('/universal/admin') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-6 py-3 text-sm font-semibold text-emerald-700 transition-colors hover:bg-emerald-100">
                        Masuk ke Panel Admin
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                @endif
            </div>

            {{-- Nomor Registrasi --}}
            @if ($status === 'pending' || $status === 'rejected')
                @if(isset($registration))
                <div class="mb-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nomor Registrasi</p>
                    </div>
                    <div class="flex flex-col items-stretch gap-3 px-6 py-5 sm:flex-row sm:items-center">
                        <code class="flex-1 break-all rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-center font-mono text-base font-bold tracking-widest text-indigo-600 sm:text-left" id="regCode">{{ $registration->registration_number }}</code>
                        <button class="flex cursor-pointer items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-600 transition-all duration-200 hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600" id="btnCopy" onclick="copyCode()" type="button">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" />
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            <span id="copyLabel">Salin</span>
                        </button>
                    </div>
                    <p class="px-6 pb-5 text-center text-xs leading-relaxed text-gray-500 sm:text-left">Simpan nomor ini untuk keperluan pelacakan atau komunikasi dengan administrator.</p>
                </div>
                @endif
            @endif

            {{-- Detail Pendaftaran --}}
            @if(isset($registration))
            <div class="mb-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Detail Pendaftaran</p>
                </div>
                @php
                    $rows = [
                        ['Nama Pemohon', $registration->applicant->name ?? 'N/A'],
                        ['Nama TPQ', $registration->tpq_name],
                        ['Nomor Statistik', $registration->tpq_registration_number ?? '-'],
                        ['No. Kontak', $registration->tpq_contact_number],
                        ['Alamat', $registration->tpq_address],
                        ['Tanggal Daftar', $registration->created_at?->format('d M Y, H:i') . ' WIB'],
                    ];
                @endphp
                <div class="px-6 py-2">
                    @foreach ($rows as [$label, $value])
                        <div class="flex flex-col gap-1 border-b border-gray-50 py-3 last:border-0 sm:flex-row sm:items-start sm:gap-4">
                            <span class="w-36 shrink-0 text-sm text-gray-500">{{ $label }}</span>
                            <span class="text-sm font-medium text-gray-800">{{ $value ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Alasan Penolakan --}}
            @if ($status === 'rejected' && !empty($rejectedReason))
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-6">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-red-600">Alasan Penolakan</p>
                    <p class="text-sm leading-relaxed text-red-800">{{ $rejectedReason }}</p>
                    <p class="mt-3 text-xs text-red-600/80">
                        Anda dapat mendaftar ulang dengan memperbaiki data sesuai catatan administrator di atas.
                    </p>
                </div>
            @endif

            {{-- Info pendaftaran pending --}}
            @if ($status === 'pending')
                <div class="mb-4 rounded-2xl border border-blue-200 bg-blue-50 p-6">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-blue-600">Informasi</p>
                    <ul class="flex list-disc flex-col gap-2 pl-5 text-sm leading-relaxed text-blue-800">
                        <li>Proses review data TPQ biasanya memakan waktu 1-2 hari kerja.</li>
                        <li>Halaman ini memperbarui setiap 5 detik secara otomatis.</li>
                        <li>Pastikan nomor kontak Anda aktif jika sewaktu-waktu admin membutuhkan verifikasi lanjutan.</li>
                    </ul>
                </div>
            @endif

            {{-- Aksi --}}
            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                @if ($status === 'rejected')
                    <a href="{{ url('/universal/admin/register') }}" class="inline-flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 shadow-sm transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2v6h-6"></path>
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        </svg>
                        Daftar Ulang
                    </a>
                @else
                    <button wire:click="$refresh" class="group inline-flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:border-gray-300 hover:bg-gray-50" type="button">
                        <svg wire:loading.class="animate-spin text-indigo-500" class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10" />
                            <path d="M3.51 15a9 9 0 1 0 .49-4l-2.99 .01" />
                        </svg>
                        Refresh Status
                    </button>
                @endif
                
                <button onclick="openLogoutModal()" class="inline-flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600 shadow-sm transition-all duration-200 hover:border-red-300 hover:bg-red-100" type="button">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Keluar
                </button>
            </div>

            <p class="mt-10 text-center text-xs text-gray-400">© {{ date('Y') }} Sistem Informasi TPQ. Semua hak dilindungi.</p>
        </div>

        {{-- Logout Confirmation Modal --}}
        <div class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/40 backdrop-blur-sm transition-opacity" id="logoutModal">
            <div class="animate-modal-in w-[90%] max-w-[380px] transform rounded-2xl bg-white p-6 shadow-xl transition-all">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">Konfirmasi Keluar</h3>
                <p class="mb-6 text-sm leading-relaxed text-gray-600">Apakah Anda yakin ingin keluar dari sesi ini? Anda masih dapat login kembali untuk mengecek status pendaftaran.</p>
                <div class="flex gap-3">
                    <button class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50" onclick="closeLogoutModal()" type="button">Batal</button>
                    
                    <form action="{{ route('logout') }}" method="POST" class="flex flex-1">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700">Ya, Keluar</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function copyCode() {
                const code = document.getElementById('regCode').innerText.trim();
                const btn = document.getElementById('btnCopy');

                navigator.clipboard.writeText(code).then(() => {
                    const originalContent = btn.innerHTML;

                    btn.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
                    btn.classList.remove('border-gray-200', 'text-gray-600', 'bg-white', 'hover:border-indigo-500', 'hover:text-indigo-600', 'hover:bg-indigo-50');
                    btn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Tersalin!</span>
                    `;

                    setTimeout(() => {
                        btn.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
                        btn.classList.add('border-gray-200', 'text-gray-600', 'bg-white', 'hover:border-indigo-500', 'hover:text-indigo-600', 'hover:bg-indigo-50');
                        btn.innerHTML = originalContent;
                    }, 2000);
                });
            }

            function openLogoutModal() {
                const modal = document.getElementById('logoutModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeLogoutModal() {
                const modal = document.getElementById('logoutModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.getElementById('logoutModal').addEventListener('click', function(e) {
                if (e.target === this) closeLogoutModal();
            });
        </script>
    </main> 
    {{-- 👆 AKHIR DARI WRAPPER TUNGGAL 👆 --}}
    
    @livewireScripts
</body>
