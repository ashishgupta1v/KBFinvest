<?php

namespace Tests\Unit;

use App\Domain\Consultation\Data\AppointmentData;
use PHPUnit\Framework\TestCase;

class AppointmentDataTest extends TestCase
{
    public function test_phone_normalization_handles_various_formats(): void
    {
        $this->assertSame('9876543210', AppointmentData::normalizePhone('9876543210'));
        $this->assertSame('9876543210', AppointmentData::normalizePhone('+91 98765 43210'));
        $this->assertSame('9876543210', AppointmentData::normalizePhone('+91-9876543210'));
        $this->assertSame('9876543210', AppointmentData::normalizePhone('09876543210'));
        $this->assertSame('9876543210', AppointmentData::normalizePhone('+91 (98765) 43210'));
    }

    public function test_to_array_returns_structured_data(): void
    {
        $dto = new AppointmentData(
            name: 'Harpreet Kaur',
            phone: '9876543210',
            email: 'harpreet@example.com',
            mode: 'Office visit',
            topic: 'Mutual Funds & SIPs',
            date: '2026-10-15',
            time: '11:30',
            notes: 'Looking for child education fund.'
        );

        $array = $dto->toArray();

        $this->assertSame('Harpreet Kaur', $array['name']);
        $this->assertSame('9876543210', $array['phone']);
        $this->assertSame('harpreet@example.com', $array['email']);
        $this->assertSame('Office visit', $array['mode']);
        $this->assertSame('Mutual Funds & SIPs', $array['topic']);
        $this->assertSame('2026-10-15', $array['date']);
        $this->assertSame('11:30', $array['time']);
        $this->assertSame('Looking for child education fund.', $array['notes']);
    }
}
