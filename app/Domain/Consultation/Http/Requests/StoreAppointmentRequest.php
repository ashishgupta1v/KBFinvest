<?php

namespace App\Domain\Consultation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $cleaned = preg_replace('/[\s\-\(\)]+/', '', (string) $this->input('phone'));
            if (str_starts_with($cleaned, '+91')) {
                $cleaned = substr($cleaned, 3);
            } elseif (str_starts_with($cleaned, '0') && strlen($cleaned) === 11) {
                $cleaned = substr($cleaned, 1);
            }
            $this->merge(['phone' => $cleaned]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'mode' => ['required', 'string', 'in:Office visit,Phone call,Video call'],
            'topic' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'website_url' => ['nullable', 'prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please provide a valid 10-digit Indian mobile number.',
            'date.after_or_equal' => 'Appointment date cannot be in the past.',
            'website_url.prohibited' => 'Invalid submission detected.',
        ];
    }
}
