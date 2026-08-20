<?php

namespace App\Services;

final readonly class ScanResult
{
    private function __construct(
        public bool $success,
        public string $level,
        public string $message,
    ) {}

    public static function success(string $message): self
    {
        return new self(true, 'success', $message);
    }

    public static function info(string $message): self
    {
        return new self(false, 'info', $message);
    }

    public static function danger(string $message): self
    {
        return new self(false, 'danger', $message);
    }
}
