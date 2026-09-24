<?php

namespace App\Domain\Consultation\Events;

use App\Domain\Consultation\Models\Appointment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentScheduled
{
    use Dispatchable, SerializesModels;

    public function __construct(public Appointment $appointment) {}
}
