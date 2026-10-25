<?php

namespace Tests\Unit;

use App\Support\PhoneNumber;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function test_it_formats_local_numbers_for_whatsapp(): void
    {
        $this->assertSame('https://wa.me/6281234567890', PhoneNumber::toWhatsAppUrl('081234567890'));
    }

    public function test_it_formats_numbers_starting_with_the_country_code(): void
    {
        $this->assertSame('https://wa.me/6281234567890', PhoneNumber::toWhatsAppUrl('6281234567890'));
    }

    public function test_it_formats_numbers_starting_with_eight(): void
    {
        $this->assertSame('https://wa.me/6281234567890', PhoneNumber::toWhatsAppUrl('81234567890'));
    }

    public function test_it_strips_non_numeric_characters(): void
    {
        $this->assertSame('https://wa.me/6281234567890', PhoneNumber::toWhatsAppUrl('+62 812-3456-7890'));
    }

    public function test_it_returns_null_for_an_empty_number(): void
    {
        $this->assertNull(PhoneNumber::toWhatsAppUrl(null));
        $this->assertNull(PhoneNumber::toWhatsAppUrl(''));
        $this->assertNull(PhoneNumber::toWhatsAppUrl('---'));
    }
}
