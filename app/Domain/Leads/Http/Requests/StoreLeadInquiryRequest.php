<?php

namespace App\Domain\Leads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadInquiryRequest extends FormRequest
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
            'city' => ['nullable', 'string', 'max:100'],
            'service_type' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
            'consent_given' => ['accepted'],
            'website_url' => ['nullable', 'prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please provide a valid 10-digit Indian mobile number.',
            'consent_given.accepted' => 'Please consent to being contacted regarding your inquiry.',
            'website_url.prohibited' => 'Invalid submission detected.',
        ];
    }
}
