<?php

namespace Tests\Feature;

use App\Domain\Consultation\Events\AppointmentScheduled;
use App\Domain\Consultation\Listeners\SendAppointmentWebhook;
use App\Domain\Consultation\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ZapierWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_dispatches_clean_payload_when_configured(): void
    {
        Http::fake([
            'https://hooks.zapier.com/*' => Http::response(['status' => 'success'], 200),
        ]);

        Config::set('services.zapier.booking_webhook_url', 'https://hooks.zapier.com/hooks/catch/123/456/');

        $appointment = Appointment::create([
            'name' => 'Simranjit Singh',
            'phone' => '9814012345',
            'email' => 'simran@example.com',
            'mode' => 'Office visit',
            'topic' => 'Mutual Funds & SIPs',
            'date' => '2026-09-30',
            'time' => '11:00 AM - 11:30 AM',
            'notes' => 'Looking for tax saving investments.',
            'status' => 'confirmed',
        ]);

        $listener = new SendAppointmentWebhook;
        $listener->handle(new AppointmentScheduled($appointment));

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'https://hooks.zapier.com/hooks/catch/123/456/'
                && $data['name'] === 'Simranjit Singh'
                && $data['phone'] === '9814012345'
                && $data['email'] === 'simran@example.com'
                && $data['mode'] === 'Office visit'
                && $data['topic'] === 'Mutual Funds & SIPs'
                && $data['date'] === '2026-09-30'
                && $data['time'] === '11:00 AM - 11:30 AM'
                && isset($data['start_time'])
                && isset($data['end_time'])
                && isset($data['calendar_summary'])
                && isset($data['calendar_description'])
                && str_contains($data['location'], 'Central Town');
        });
    }

    public function test_webhook_skips_silently_when_url_is_not_configured(): void
    {
        Http::fake();

        Config::set('services.zapier.booking_webhook_url', null);

        $appointment = Appointment::create([
            'name' => 'Simranjit Singh',
            'phone' => '9814012345',
            'mode' => 'Phone call',
            'topic' => 'Loans',
            'date' => '2026-09-30',
            'time' => '14:00',
            'status' => 'confirmed',
        ]);

        $listener = new SendAppointmentWebhook;
        $listener->handle(new AppointmentScheduled($appointment));

        Http::assertNothingSent();
    }

    public function test_artisan_command_sends_test_webhook(): void
    {
        Http::fake([
            'https://hooks.zapier.com/*' => Http::response(['status' => 'success'], 200),
        ]);

        $this->artisan('zapier:test-webhook', [
            'url' => 'https://hooks.zapier.com/hooks/catch/test/test/',
        ])
            ->expectsOutputToContain('Success! Webhook accepted')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://hooks.zapier.com/hooks/catch/test/test/'
                && $request['name'] === 'Simranjit Singh';
        });
    }
}
