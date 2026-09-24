<?php

namespace App\Domain\Consultation\Actions;

use App\Domain\Consultation\Data\AppointmentData;
use App\Domain\Consultation\Events\AppointmentScheduled;
use App\Domain\Consultation\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CreateAppointmentAction
{
    public function execute(AppointmentData $data): Appointment
    {
        // Guard against duplicate bookings for identical phone + date + time slot
        $existing = Appointment::where('date', $data->date)
            ->where('time', $data->time)
            ->where('phone', $data->phone)
            ->first();

        if ($existing) {
            $message = 'An appointment for this phone number is already scheduled for this date and time slot.';

            throw ValidationException::withMessages([
                'time' => $message,
                'phone' => $message,
                'conflict' => $message,
            ]);
        }

        $appointment = Appointment::create($data->toArray());

        AppointmentScheduled::dispatch($appointment);

        Log::info('consultation.appointment.scheduled', [
            'appointment_id' => $appointment->id,
            'name' => $appointment->name,
            'phone' => $appointment->phone,
            'date' => $appointment->date,
            'time' => $appointment->time,
            'mode' => $appointment->mode,
            'topic' => $appointment->topic,
        ]);

        return $appointment;
    }
}
