<?php

namespace Tests\Feature;

use App\Domain\Leads\Events\LeadCaptured;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LeadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_lead_inquiry_submission(): void
    {
        Event::fake([LeadCaptured::class]);

        $payload = [
            'name' => 'Kuldeep Verma',
            'phone' => '+91 98765-43210',
            'email' => 'kuldeep@example.com',
            'city' => 'Ludhiana',
            'service_type' => 'Mutual Funds',
            'details' => 'Looking to start monthly SIP portfolio for wealth creation.',
            'consent_given' => true,
        ];

        $response = $this->from('/contact')->post('/contact', $payload);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lead_inquiries', [
            'name' => 'Kuldeep Verma',
            'phone' => '9876543210',
            'city' => 'Ludhiana',
            'service_type' => 'Mutual Funds',
        ]);

        Event::assertDispatched(LeadCaptured::class, function ($event) {
            return $event->lead->name === 'Kuldeep Verma';
        });
    }

    public function test_honeypot_rejects_lead_bot(): void
    {
        $payload = [
            'name' => 'Bot Lead',
            'phone' => '9876543210',
            'service_type' => 'Mutual Funds',
            'consent_given' => true,
            'website_url' => 'https://bot-farm.xyz',
        ];

        $response = $this->from('/contact')->post('/contact', $payload);
        $response->assertSessionHasErrors(['website_url']);
    }

    public function test_phone_validation_rejects_invalid_number(): void
    {
        $payload = [
            'name' => 'Invalid Phone Lead',
            'phone' => '0000000000',
            'service_type' => 'Mutual Funds',
            'consent_given' => true,
        ];

        $response = $this->from('/contact')->post('/contact', $payload);
        $response->assertSessionHasErrors(['phone']);
    }

    public function test_consent_is_required(): void
    {
        $payload = [
            'name' => 'No Consent Lead',
            'phone' => '9876543210',
            'service_type' => 'Mutual Funds',
            'consent_given' => false,
        ];

        $response = $this->from('/contact')->post('/contact', $payload);
        $response->assertSessionHasErrors(['consent_given']);
    }
}
