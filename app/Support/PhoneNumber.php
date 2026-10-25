<?php

namespace App\Support;

final class PhoneNumber
{
    public static function toWhatsAppUrl(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);

        if ($digits === '') {
            return null;
        }

        $digits = match (true) {
            str_starts_with($digits, '0') => '62' . substr($digits, 1),
            str_starts_with($digits, '8') => '62' . $digits,
            default => $digits,
        };

        return "https://wa.me/{$digits}";
    }
}
