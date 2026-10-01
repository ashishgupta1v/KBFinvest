<?php

namespace App\Domain\Consultation\Listeners;

use App\Domain\Consultation\Events\AppointmentScheduled;
use App\Domain\Consultation\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendAppointmentWebhook
{
    /** Maximum retry attempts for rate-limited / queue-full responses. */
    private const MAX_RETRIES = 3;

    /**
     * Handle the event.
     */
    public function handle(AppointmentScheduled $event): void
    {
        $appointment = $event->appointment;
        $url = config('services.zapier.booking_webhook_url');

        if (empty($url)) {
            Log::warning('zapier.booking_webhook.skipped', [
                'reason' => 'MAKE_BOOKING_WEBHOOK_URL is not configured',
                'appointment_id' => $appointment->id,
            ]);

            return;
        }

        try {
            $payload = $this->buildPayload($appointment);
            $this->dispatchWithRetry($url, $payload, $appointment);
        } catch (\Throwable $e) {
            Log::error('zapier.booking_webhook.failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * POST the payload to the webhook URL, retrying on 400/429 queue-full responses.
     *
     * @param  array<string, mixed>  $payload
     */
    private function dispatchWithRetry(string $url, array $payload, Appointment $appointment): void
    {
        $attempt = 0;

        do {
            $attempt++;

            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => 'KBFinvest-Webhook/1.0',
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info('zapier.booking_webhook.sent', [
                    'appointment_id' => $appointment->id,
                    'status' => $response->status(),
                    'attempt' => $attempt,
                ]);

                return;
            }

            // 400 "Queue is full" or 429 rate-limit — retry with backoff
            $isRetryable = in_array($response->status(), [400, 429, 503])
                && str_contains(strtolower($response->body()), 'queue');

            if ($isRetryable && $attempt < self::MAX_RETRIES) {
                $backoffSeconds = 2 ** ($attempt - 1); // 1s, 2s, 4s
                Log::info('zapier.booking_webhook.retrying', [
                    'appointment_id' => $appointment->id,
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'backoff_seconds' => $backoffSeconds,
                ]);
                sleep($backoffSeconds);

                continue;
            }

            // Final failure after retries or a non-retryable error
            $context = [
                'appointment_id' => $appointment->id,
                'status' => $response->status(),
                'body' => $response->body(),
                'attempts' => $attempt,
                'url' => $url,
            ];

            if ($response->status() === 400 && str_contains(strtolower($response->body()), 'queue')) {
                Log::error('zapier.booking_webhook.queue_full', array_merge($context, [
                    'action_required' => 'Make.com scenario queue is full. Go to make.com, open the KB Finvest scenario, and clear the queue or upgrade the plan.',
                    'calendar_fallback' => 'Client can still use the Add to Google Calendar button on the confirmation screen.',
                ]));
            } else {
                Log::warning('zapier.booking_webhook.http_error', $context);
            }

            return;
        } while ($attempt < self::MAX_RETRIES);
    }

    /**
     * Build the structured payload formatted for Zapier & Google Calendar.
     *
     * @return array<string, mixed>
     */
    public function buildPayload(Appointment $appointment): array
    {
        [$startTime, $endTime, $startFormatted, $endFormatted] = $this->resolveStartAndEndTime($appointment->date, $appointment->time);

        $location = match ($appointment->mode) {
            'Office visit' => 'KB Finvest, 179-A, St. No. 1, Central Town, Near Hotel Keys, Ludhiana, Punjab 142022 (https://maps.app.goo.gl/KuroHh51TGhBCMMz7?g_st=aw)',
            'Phone call' => 'Phone Call (+91 79734 61669 / '.$appointment->phone.')',
            'Video call' => 'Google Meet / Online Video Call',
            default => 'KB Finvest, Ludhiana',
        };

        $summary = "KB Finvest Consultation — {$appointment->name} ({$appointment->topic})";

        $description = implode("\n", [
            'KB Finvest Appointment Confirmation',
            '------------------------------------',
            "Client Name: {$appointment->name}",
            "Phone: {$appointment->phone}",
            'Email: '.($appointment->email ?: 'Not provided'),
            "Topic: {$appointment->topic}",
            "Mode: {$appointment->mode}",
            "Date: {$appointment->date}",
            "Time: {$appointment->time}",
            "Location: {$location}",
            'Client Notes: '.($appointment->notes ?: 'None'),
            '',
            'Consultant: Kulwinder Singh (+91 79734 61669)',
            'Booked through: KB Finvest Website (Central Town, Ludhiana)',
        ]);

        return [
            'event' => 'consultation.appointment.scheduled',
            'appointment_id' => $appointment->id,
            'name' => $appointment->name,
            'phone' => $appointment->phone,
            'email' => $appointment->email ?: 'kbfinvest123@gmail.com',
            'client_email' => $appointment->email ?: 'Not provided',
            'mode' => $appointment->mode,
            'topic' => $appointment->topic,
            'date' => $appointment->date,
            'time' => $appointment->time,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'start_date_time' => $startFormatted,
            'end_date_time' => $endFormatted,
            'location' => $location,
            'notes' => $appointment->notes ?: '',
            'calendar_summary' => $summary,
            'summary' => $summary,
            'calendar_description' => $description,
            'description' => $description,
            'start' => $startTime,
            'end' => $endTime,
            'consultant_name' => 'Kulwinder Singh',
            'consultant_phone' => '+91 79734 61669',
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Resolve ISO/RFC formatted start and end times for Google Calendar.
     *
     * @return array{0: string, 1: string, 2: string, 3: string}
     */
    protected function resolveStartAndEndTime(string $date, string $time): array
    {
        // Check if time is like "11:00 AM - 11:30 AM" or "11:00 AM" or "14:30"
        $parts = explode('-', $time);
        $startRaw = trim($parts[0]);
        $endRaw = isset($parts[1]) ? trim($parts[1]) : null;

        try {
            $start = Carbon::parse("{$date} {$startRaw}", 'Asia/Kolkata');
        } catch (\Throwable) {
            $start = Carbon::parse($date, 'Asia/Kolkata')->setTime(10, 0);
        }

        if ($endRaw) {
            try {
                $end = Carbon::parse("{$date} {$endRaw}", 'Asia/Kolkata');
            } catch (\Throwable) {
                $end = (clone $start)->addMinutes(30);
            }
        } else {
            $end = (clone $start)->addMinutes(30);
        }

        return [
            $start->toIso8601String(),
            $end->toIso8601String(),
            $start->format('Y-m-d H:i'),
            $end->format('Y-m-d H:i'),
        ];
    }
}
