<x-filament-panels::page>
    <div x-data="qrScanner()" x-init="initScanner" class="flex w-full flex-col items-center">

        {{-- CAMERA --}}
        <div wire:ignore
            class="w-full max-w-[700px] overflow-hidden rounded-xl bg-black shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <div id="reader" class="w-full"></div>
        </div>

        {{-- CONTROL --}}
        <div id="scanner-controls"
            class="mt-4 flex w-full max-w-[700px] flex-col gap-3 sm:flex-row sm:items-center sm:justify-center">
        </div>

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
                                    showTorchButtonIfSupported: true,
                                },
                                false
                            );

                            this.scanner.render(
                                this.onScanSuccess.bind(this),
                                this.onScanError.bind(this)
                            );

                            this.moveControls();
                        }, 500);
                    },

                    moveControls() {
                        const controls = document.querySelector(
                            '#reader__dashboard_section_csr'
                        );

                        const target = document.querySelector(
                            '#scanner-controls'
                        );

                        if (controls && target) {
                            target.appendChild(controls);
                        }
                    },

                    onScanSuccess(decodedText, decodedResult) {
                        this.scanner.pause(true);

                        this.$wire.processQrScan(decodedText)
                            .then(() => {
                                setTimeout(() => {
                                    this.scanner.resume();
                                }, 2000);
                            })
                            .catch(() => {
                                console.error(
                                    'Gagal memproses scan absensi.'
                                );

                                setTimeout(() => {
                                    this.scanner.resume();
                                }, 2000);
                            });
                    },

                    onScanError(error) {
                        // Abaikan error scanning
                    }
                }))
            })
        </script>

        <style>
            /* =========================
               CAMERA
            ========================= */
            #reader {
                width: min(100%,
                        65vh,
                        650px) !important;

                aspect-ratio: 1 / 1 !important;

                border: none !important;
                background: #000 !important;
                margin: 0 auto !important;
            }

            #reader video {
                width: 100% !important;
                height: 100% !important;
                display: block !important;
                object-fit: cover !important;
            }

            #reader img,
            #reader a {
                display: none !important;
            }


            /* =========================
               HIDE ORIGINAL DASHBOARD
               FROM CAMERA
            ========================= */

            #reader__dashboard_section_csr {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #reader__dashboard_section_csr select {
                width: 100% !important;
                padding: 0.7rem 1rem !important;

                border-radius: 0.5rem !important;
                border: 1px solid rgb(63 63 70) !important;

                background-color: rgb(24 24 27) !important;
                color: white !important;

                outline: none !important;
                font-size: 0.95rem !important;
            }

            #reader__dashboard_section_csr select option {
                background-color: rgb(24 24 27) !important;
                color: white !important;
            }

            #reader__dashboard_section_csr button {
                width: 100% !important;

                padding: 0.7rem 1.5rem !important;

                border: none !important;
                border-radius: 0.5rem !important;

                background-color: rgb(217 119 6) !important;
                color: white !important;

                font-weight: 600 !important;
                cursor: pointer !important;

                transition: background-color 0.2s ease-in-out !important;
            }

            #reader__dashboard_section_csr button:hover {
                background-color: rgb(180 83 9) !important;
            }

            @media (min-width: 640px) {

                #reader__dashboard_section_csr {
                    flex-direction: row !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                #reader__dashboard_section_csr select {
                    width: 300px !important;
                }

                #reader__dashboard_section_csr button {
                    width: auto !important;
                }
            }
        </style>

    </div>
</x-filament-panels::page>
