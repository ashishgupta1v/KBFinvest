<?php

namespace App\Domain\Shared;

class SiteConfig
{
    public static function get(): array
    {
        return [
            'brand' => 'KB Finvest',
            'tagline' => 'Invest Smart. Grow Secure.',
            'owner' => 'Kulwinder Singh',
            'ownerRole' => 'Founder · Mutual Fund Distributor',
            'photo' => 'kulwinder-singh.jpg',
            'photoPos' => '50% 22%',
            'phone' => '+917973461669',
            'phoneDisplay' => '+91 79734 61669',
            'wa' => '917973461669',
            'email' => 'ks_sandhu07@yahoo.co.in',
            'addr' => [
                'line1' => '179-A, St. No. 1, Central Town',
                'line2' => 'Near Hotel Keys',
                'city' => 'Ludhiana',
                'state' => 'Punjab',
                'pin' => '',
                'country' => 'IN',
            ],
            'hoursLabel' => 'Mon–Fri 9:30 am – 6:30 pm · Sat 9:30 am – 5:30 pm · Sun closed',
            'hours' => [
                1 => [570, 1110],
                2 => [570, 1110],
                3 => [570, 1110],
                4 => [570, 1110],
                5 => [570, 1110],
                6 => [570, 1050],
                0 => null,
            ],
            'consentVersion' => '2026-09-20.1',
        ];
    }

    public static function navigation(): array
    {
        return [
            ['id' => 'home', 'label' => 'Home', 'url' => '/'],
            ['id' => 'investments', 'label' => 'Investments', 'url' => '/investments'],
            ['id' => 'insurance', 'label' => 'Insurance', 'url' => '/insurance'],
            ['id' => 'loans', 'label' => 'Loans', 'url' => '/loans'],
            ['id' => 'advisory', 'label' => 'Advisory', 'url' => '/advisory'],
            ['id' => 'calculators', 'label' => 'Calculators', 'url' => '/calculators'],
            ['id' => 'resources', 'label' => 'Resources', 'url' => '/resources'],
            ['id' => 'about', 'label' => 'About', 'url' => '/about'],
            ['id' => 'contact', 'label' => 'Contact', 'url' => '/contact'],
        ];
    }
}
