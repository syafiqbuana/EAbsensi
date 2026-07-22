<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Mengirim pesan WhatsApp melalui Fonnte
     *
     * @param string $target Nomor tujuan (contoh: 0812xxx atau 62812xxx)
     * @param string $message Isi pesan
     * @return bool
     */
    public function sendMessage(string $target, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                        'target' => $target,
                        'message' => $message,
                        'countryCode' => '62',
                    ]);

            $responseData = $response->json();

            // Cek HTTP status DAN cek key 'status' di dalam JSON Fonnte
            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === true) {
                return true;
            }

            // Sekarang, error dari Fonnte (seperti invalid token/device offline) akan masuk ke log
            Log::error('Fonnte API Gagal:', [
                'response' => $responseData,
                'target' => $target
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Fonnte Service Exception: ' . $e->getMessage());
            return false;
        }
    }
}