<?php

namespace App\Domain\Consultation\Data;

use App\Domain\Consultation\Http\Requests\StoreAppointmentRequest;

readonly class AppointmentData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public string $mode,
        public string $topic,
        public string $date,
        public string $time,
        public ?string $notes = null,
    ) {}

    public static function fromRequest(StoreAppointmentRequest $request): self
    {
        $rawPhone = (string) $request->input('phone');
        $normalizedPhone = self::normalizePhone($rawPhone);

        return new self(
            name: trim((string) $request->input('name')),
            phone: $normalizedPhone,
            email: $request->filled('email') ? trim((string) $request->input('email')) : null,
            mode: (string) $request->input('mode'),
            topic: (string) $request->input('topic'),
            date: (string) $request->input('date'),
            time: (string) $request->input('time'),
            notes: $request->filled('notes') ? trim((string) $request->input('notes')) : null,
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
            'mode' => $this->mode,
            'topic' => $this->topic,
            'date' => $this->date,
            'time' => $this->time,
            'notes' => $this->notes,
        ];
    }
}
