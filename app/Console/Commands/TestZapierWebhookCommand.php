<?php

namespace App\Console\Commands;

use App\Domain\Consultation\Listeners\SendAppointmentWebhook;
use App\Domain\Consultation\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestZapierWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapier:test-webhook {url? : Optional webhook URL to test} {--appointment= : Optional ID of an existing appointment to send}';

    /**
     * The console command aliases.
     *
     * @var array<int, string>
     */
    protected $aliases = ['booking:test-webhook', 'make:test-webhook'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch a sample appointment payload to the Make.com / Zapier webhook to test Google Calendar integration';

    /**
     * Execute the console command.
     */
    public function handle(SendAppointmentWebhook $listener): int
    {
        $url = $this->argument('url') ?: config('services.zapier.booking_webhook_url');

        if (empty($url)) {
            $this->error('No Zapier webhook URL provided.');
            $this->line('Set ZAPIER_BOOKING_WEBHOOK_URL in your .env or pass it as an argument:');
            $this->line('php artisan zapier:test-webhook https://hooks.zapier.com/hooks/catch/xxxxxx/yyyyyy/');

            return self::FAILURE;
        }

        $this->info("Sending test payload to: {$url}");

        $appointmentId = $this->option('appointment');
        if ($appointmentId) {
            $appointment = Appointment::find($appointmentId);
            if (! $appointment) {
                $this->error("Appointment #{$appointmentId} not found in database.");

                return self::FAILURE;
            }
        } else {
            // Create an in-memory sample appointment
            $appointment = new Appointment([
                'name' => 'Simranjit Singh',
                'phone' => '9814012345',
                'email' => 'simranjit@example.com',
                'mode' => 'Office visit',
                'topic' => 'Mutual Funds & SIPs',
                'date' => now()->addDays(2)->format('Y-m-d'),
                'time' => '11:00 AM - 11:30 AM',
                'notes' => 'Looking to start monthly SIPs for children higher education.',
                'status' => 'confirmed',
            ]);
            $appointment->id = 999;
        }

        $payload = $listener->buildPayload($appointment);

        $this->line('Payload:');
        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        try {
            $response = Http::timeout(10)->post($url, $payload);

            if ($response->successful()) {
                $this->info("✓ Success! Webhook accepted with status: {$response->status()}");
                $this->line('Response: '.$response->body());

                return self::SUCCESS;
            }

            $this->error("Webhook returned error status: {$response->status()}");
            $this->line('Response: '.$response->body());

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error("Request failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
