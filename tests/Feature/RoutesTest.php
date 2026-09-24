<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render_inertia_successfully(): void
    {
        $routes = [
            '/' => 'Home/Index',
            '/investments' => 'Investments/Index',
            '/insurance' => 'Insurance/Index',
            '/loans' => 'Loans/Index',
            '/advisory' => 'Advisory/Index',
            '/calculators' => 'Calculators/Index',
            '/resources' => 'Resources/Index',
            '/about' => 'About/Index',
            '/book' => 'Book/Index',
            '/contact' => 'Contact/Index',
            '/legal' => 'Legal/Show',
            '/legal/privacy' => 'Legal/Show',
            '/legal/terms' => 'Legal/Show',
            '/legal/grievance' => 'Legal/Show',
        ];

        foreach ($routes as $url => $component) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertInertia(fn (Assert $page) => $page->component($component));
        }
    }

    public function test_contact_form_submission(): void
    {
        $payload = [
            'name' => 'Gurpreet Singh',
            'phone' => '9876543210',
            'email' => 'gurpreet@example.com',
            'city' => 'Ludhiana',
            'service_type' => 'Mutual Funds',
            'details' => 'Looking to start monthly SIP portfolio.',
            'consent_given' => true,
        ];

        $response = $this->from('/contact')->post('/contact', $payload);
        $response->assertRedirect('/contact');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lead_inquiries', [
            'name' => 'Gurpreet Singh',
            'phone' => '9876543210',
            'city' => 'Ludhiana',
        ]);
    }

    public function test_book_appointment_submission(): void
    {
        $payload = [
            'name' => 'Aman Sharma',
            'phone' => '9812345678',
            'email' => 'aman@example.com',
            'mode' => 'Office visit',
            'topic' => 'Portfolio Review',
            'date' => date('Y-m-d', strtotime('+3 days')),
            'time' => '11:00 AM - 11:30 AM',
            'notes' => 'Review existing equity funds and insurance.',
        ];

        $response = $this->from('/book')->post('/book', $payload);
        $response->assertRedirect('/book');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'name' => 'Aman Sharma',
            'phone' => '9812345678',
        ]);
    }
}
