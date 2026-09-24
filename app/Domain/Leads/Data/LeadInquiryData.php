<?php

namespace App\Domain\Leads\Data;

use App\Domain\Leads\Http\Requests\StoreLeadInquiryRequest;

readonly class LeadInquiryData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public ?string $city,
        public string $service_type,
        public ?string $details,
        public bool $consent_given,
        public ?string $ip_address = null,
    ) {}

    public static function fromRequest(StoreLeadInquiryRequest $request): self
    {
        $rawPhone = (string) $request->input('phone');
        $normalizedPhone = self::normalizePhone($rawPhone);

        return new self(
            name: trim((string) $request->input('name')),
            phone: $normalizedPhone,
            email: $request->filled('email') ? trim((string) $request->input('email')) : null,
            city: $request->filled('city') ? trim((string) $request->input('city')) : null,
            service_type: (string) $request->input('service_type'),
            details: $request->filled('details') ? trim((string) $request->input('details')) : null,
            consent_given: (bool) $request->boolean('consent_given'),
            ip_address: $request->ip(),
        );
    }

    public static function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone);
        if (str_starts_with($cleaned, '+91')) {
            $cleaned = substr($cleaned, 3);
        } elseif (str_starts_with($cleaned, '0') && strlen($cleaned) === 11) {
            $cleaned = substr($cleaned, 1);
        }

        return $cleaned;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'city' => $this->city,
            'service_type' => $this->service_type,
            'details' => $this->details,
            'consent_given' => $this->consent_given,
            'ip_address' => $this->ip_address,
        ];
    }
}
