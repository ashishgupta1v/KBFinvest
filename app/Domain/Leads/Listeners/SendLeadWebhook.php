<?php

namespace App\Domain\Leads\Listeners;

use App\Domain\Leads\Events\LeadCaptured;
use App\Domain\Leads\Models\LeadInquiry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLeadWebhook
{
    /**
     * Handle the event.
     */
    public function handle(LeadCaptured $event): void
    {
        $lead = $event->lead;
        $url = config('services.zapier.lead_webhook_url');

        if (empty($url)) {
            return;
        }

        try {
            $payload = $this->buildPayload($lead);

            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'KBFinvest-Webhook/1.0',
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info('zapier.lead_webhook.sent', [
                    'lead_id' => $lead->id,
                    'status' => $response->status(),
                ]);
            } else {
                Log::warning('zapier.lead_webhook.http_error', [
                    'lead_id' => $lead->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('zapier.lead_webhook.failed', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build the structured payload for Make.com / Zapier / Google Sheets CRM.
     *
     * @return array<string, mixed>
     */
    public function buildPayload(LeadInquiry $lead): array
    {
        return [
            'event' => 'lead.inquiry.captured',
            'lead_id' => $lead->id,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email ?: 'Not provided',
            'city' => $lead->city ?: 'Ludhiana',
            'service_type' => $lead->service_type,
            'investment_horizon' => $lead->investment_horizon ?: 'Not specified',
            'estimated_amount' => $lead->estimated_amount ?: 'Not specified',
            'message' => $lead->message ?: 'None',
            'ip_address' => $lead->ip_address,
            'consultant_name' => 'Kulwinder Singh',
            'consultant_phone' => '+91 79734 61669',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
