<?php

namespace Tests\Feature;

use App\Domain\Consultation\Events\AppointmentScheduled;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_appointment_booking(): void
    {
        Event::fake([AppointmentScheduled::class]);

        $payload = [
            'name' => 'Simranjit Singh',
            'phone' => '+91 98140 12345',
            'email' => 'simran@example.com',
            'mode' => 'Office visit',
            'topic' => 'Mutual Funds & SIPs',
            'date' => date('Y-m-d', strtotime('+2 days')),
            'time' => '11:30',
            'notes' => 'Planning for 10-year horizon portfolio.',
        ];

        $response = $this->from('/book')->post('/book', $payload);

        $response->assertRedirect('/book');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'name' => 'Simranjit Singh',
            'phone' => '9814012345',
            'time' => '11:30',
        ]);

        Event::assertDispatched(AppointmentScheduled::class, function ($event) {
            return $event->appointment->name === 'Simranjit Singh';
        });
    }

    public function test_duplicate_slot_booking_by_same_phone_is_rejected(): void
    {
        $payload = [
            'name' => 'Simranjit Singh',
            'phone' => '9814012345',
            'email' => 'simran@example.com',
            'mode' => 'Office visit',
            'topic' => 'Mutual Funds & SIPs',
            'date' => date('Y-m-d', strtotime('+3 days')),
            'time' => '14:30',
        ];

        $first = $this->from('/book')->post('/book', $payload);
        $first->assertRedirect('/book');
        $first->assertSessionHas('success');

        $second = $this->from('/book')->post('/book', $payload);
        $second->assertRedirect('/book');
        $second->assertSessionHasErrors([
            'time' => 'An appointment for this phone number is already scheduled for this date and time slot.',
            'phone' => 'An appointment for this phone number is already scheduled for this date and time slot.',
            'conflict' => 'An appointment for this phone number is already scheduled for this date and time slot.',
        ]);
    }

    public function test_honeypot_field_rejects_bot_submission(): void
    {
        $payload = [
            'name' => 'Spam Bot',
            'phone' => '9814012345',
            'mode' => 'Office visit',
            'topic' => 'General Financial Planning',
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
            'website_url' => 'https://spammy-site.com',
        ];

        $response = $this->from('/book')->post('/book', $payload);
        $response->assertSessionHasErrors(['website_url']);
    }

    public function test_invalid_phone_number_is_rejected(): void
    {
        $payload = [
            'name' => 'Invalid Phone User',
            'phone' => '12345',
            'mode' => 'Office visit',
            'topic' => 'General Financial Planning',
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
        ];

        $response = $this->from('/book')->post('/book', $payload);
        $response->assertSessionHasErrors(['phone']);
    }

    public function test_past_date_is_rejected(): void
    {
        $payload = [
            'name' => 'Past Date User',
            'phone' => '9814012345',
            'mode' => 'Office visit',
            'topic' => 'General Financial Planning',
            'date' => '2020-01-01',
            'time' => '10:00',
        ];

        $response = $this->from('/book')->post('/book', $payload);
        $response->assertSessionHasErrors(['date']);
    }
}
