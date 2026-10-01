<?php

namespace Tests\Feature;

use App\Domain\Leads\Events\LeadCaptured;
use App\Domain\Leads\Listeners\SendLeadWebhook;
use App\Domain\Leads\Models\LeadInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LeadWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_webhook_dispatches_clean_payload_when_configured(): void
    {
        Http::fake([
            'https://hooks.make.com/*' => Http::response(['status' => 'success'], 200),
        ]);

        Config::set('services.zapier.lead_webhook_url', 'https://hooks.make.com/leads/test-hook');

        $lead = LeadInquiry::create([
            'name' => 'Harpreet Singh',
            'phone' => '9876543210',
            'email' => 'harpreet@example.com',
            'city' => 'Ludhiana',
            'service_type' => 'mutual_funds',
            'investment_horizon' => '5-10 years',
            'estimated_amount' => '₹5 Lakhs - ₹10 Lakhs',
            'message' => 'Looking for tax-saving ELSS schemes.',
            'ip_address' => '127.0.0.1',
        ]);

        $listener = new SendLeadWebhook;
        $listener->handle(new LeadCaptured($lead));

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'https://hooks.make.com/leads/test-hook'
                && $data['event'] === 'lead.inquiry.captured'
                && $data['name'] === 'Harpreet Singh'
                && $data['phone'] === '9876543210'
                && $data['email'] === 'harpreet@example.com'
                && $data['city'] === 'Ludhiana'
                && $data['service_type'] === 'mutual_funds'
                && $data['consultant_name'] === 'Kulwinder Singh'
                && isset($data['timestamp']);
        });
    }

    public function test_lead_webhook_skips_when_url_is_not_configured(): void
    {
        Http::fake();

        Config::set('services.zapier.lead_webhook_url', null);

        $lead = LeadInquiry::create([
            'name' => 'Gurinder Kaur',
            'phone' => '9815098765',
            'city' => 'Jalandhar',
            'service_type' => 'insurance',
        ]);

        $listener = new SendLeadWebhook;
        $listener->handle(new LeadCaptured($lead));

        Http::assertNothingSent();
    }

    public function test_contact_form_submission_dispatches_webhook_and_redirects(): void
    {
        Http::fake([
            'https://hooks.make.com/*' => Http::response(['status' => 'accepted'], 200),
        ]);

        Config::set('services.zapier.lead_webhook_url', 'https://hooks.make.com/leads/test-hook');

        $response = $this->from('/contact')->post('/contact', [
            'name' => 'Baljit Singh',
            'phone' => '9872012345',
            'email' => 'baljit@example.com',
            'city' => 'Ludhiana',
            'service_type' => 'mutual_funds',
            'details' => 'Need advice on portfolio reallocation.',
            'consent_given' => true,
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lead_inquiries', [
            'name' => 'Baljit Singh',
            'phone' => '9872012345',
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://hooks.make.com/leads/test-hook'
                && $request['name'] === 'Baljit Singh';
        });
    }

    public function test_contact_form_submission_handles_webhook_error_resiliently(): void
    {
        Http::fake([
            'https://hooks.make.com/*' => Http::response('Internal Server Error', 500),
        ]);

        Config::set('services.zapier.lead_webhook_url', 'https://hooks.make.com/leads/test-hook');

        $response = $this->from('/contact')->post('/contact', [
            'name' => 'Navjot Singh',
            'phone' => '9814199999',
            'email' => 'navjot@example.com',
            'city' => 'Ludhiana',
            'service_type' => 'loans',
            'consent_given' => true,
        ]);

        // The user must still see a successful redirect, and the lead must be safely persisted in DB
        $response->assertRedirect('/contact');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lead_inquiries', [
            'name' => 'Navjot Singh',
            'phone' => '9814199999',
        ]);
    }

    public function test_lead_webhook_does_not_fall_back_to_booking_webhook_url(): void
    {
        Http::fake();

        // Booking webhook is set, but lead webhook is NULL
        Config::set('services.zapier.booking_webhook_url', 'https://hooks.make.com/bookings/calendar-only');
        Config::set('services.zapier.lead_webhook_url', null);

        $lead = LeadInquiry::create([
            'name' => 'General Inquiry',
            'phone' => '9814112345',
            'city' => 'Ludhiana',
            'service_type' => 'insurance',
        ]);

        $listener = new SendLeadWebhook;
        $listener->handle(new LeadCaptured($lead));

        // Must NOT send to booking webhook URL
        Http::assertNothingSent();
    }
}
