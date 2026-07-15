<x-filament-panels::page>
    <div x-data="qrScanner()" x-init="initScanner" class="flex flex-col items-center w-full">
        {{-- Pembungkus Kamera --}}
        <div class="w-full max-w-lg p-4 bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            wire:ignore>
            <div id="reader" class="w-full"></div>
        </div>

        {{-- GANTI CDN DENGAN VITE DI SINI --}}
        @vite('resources/js/app.js')

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('qrScanner', () => ({
                    scanner: null,

                    initScanner() {
                        setTimeout(() => {

                            this.scanner = new window.Html5QrcodeScanner(
                                "reader", {
                                    fps: 10,
                                    aspectRatio: 1.0,
                                    supportedScanTypes: [0],
                                    showTorchButtonIfSupported: true, // <-- Tambahkan ini
                                },
                                false
                            );

                            this.scanner.render(this.onScanSuccess.bind(this), this.onScanError
                                .bind(this));
                        }, 500);
                    },

                    onScanSuccess(decodedText, decodedResult) {
                        this.scanner.pause(true);

                        $wire.processQrScan(decodedText).then(() => {
                            setTimeout(() => {
                                this.scanner.resume();
                            }, 2000);
                        });
                    },

                    onScanError(error) {
                        // Abaikan
                    }
                }))
            })
        </script>

<style>
    /* 1. HAPUS BORDER & PASTIKAN CONTAINER FULL WIDTH */
    #reader {
        border: none !important;
        background: transparent !important;
        width: 100% !important;
    }

    /* HILANGKAN GAMBAR/ICON BAWAAN LIBRARY */
    #reader img {
        display: none !important;
    }

    /* 2. RESPONSIVE CONTAINER UNTUK KONTROL (FLEXBOX) */
    #reader__dashboard_section_csr {
        display: flex !important;
        flex-direction: column !important; /* Tumpuk ke bawah di HP */
        gap: 10px !important; /* Jarak antar elemen */
        padding: 10px 0 !important;
        text-align: center !important;
    }

    /* 3. PERBAIKI DROPDOWN SELECT CAMERA */
    #reader__dashboard_section_csr select {
        padding: 0.75rem 1rem !important; /* Padding sedikit lebih besar untuk sentuhan jari */
        border-radius: 0.5rem !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        background-color: #18181b !important;
        color: #ffffff !important;
        width: 100% !important; /* Full width di HP */
        outline: none !important;
        font-size: 0.95rem !important;
        cursor: pointer !important;
        margin: 0 !important;
    }

    #reader__dashboard_section_csr select option {
        background-color: #18181b !important;
        color: #ffffff !important;
    }

    /* 4. PERCANTIK TOMBOL START / STOP SCANNING */
    #reader__dashboard_section_csr button {
        background-color: rgb(217, 119, 6) !important; /* Warna Amber Filament */
        color: white !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 0.5rem !important;
        border: none !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        width: 100% !important; /* Full width di HP */
        margin: 0 !important;
        transition: background-color 0.2s ease-in-out !important;
    }

    #reader__dashboard_section_csr button:hover {
        background-color: rgb(180, 83, 9) !important;
    }

    /* 5. PASTIKAN VIDEO KAMERA TIDAK PENYOK */
    #reader video {
        width: 100% !important;
        height: auto !important;
        border-radius: 0.5rem !important; /* Sudut membulat agar rapi */
        object-fit: cover !important; 
    }

    /* Sembunyikan elemen link / text yang tidak perlu */
    #reader a {
        display: none !important;
    }

    /* 6. MEDIA QUERY UNTUK TABLET & LAYAR LAPTOP (MIN-WIDTH: 640px) */
    @media (min-width: 640px) {
        #reader__dashboard_section_csr {
            flex-direction: row !important; /* Sejajarkan ke samping jika muat */
            justify-content: center !important;
            align-items: center !important;
        }
        
        #reader__dashboard_section_csr select {
            width: auto !important; /* Kembalikan ke ukuran auto */
            max-width: 350px !important;
        }
        
        #reader__dashboard_section_csr button {
            width: auto !important; /* Kembalikan ke ukuran auto */
        }
    }
</style>
    </div>
</x-filament-panels::page>
