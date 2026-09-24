<?php

namespace App\Domain\Compliance\Services;

class LegalCatalogService
{
    public static function getData(string $slug = 'disclosures'): array
    {
        $docs = [
            'disclosures' => [
                'title' => 'Regulatory Disclosures',
                'sections' => [
                    [
                        'heading' => 'Who we are',
                        'content' => 'KB Finvest is the financial distribution and facilitation practice of Kulwinder Singh, located at 179-A, St. No. 1, Central Town, Near Hotel Keys, Ludhiana, Punjab.',
                    ],
                    [
                        'heading' => 'Remuneration & Commissions',
                        'content' => 'On Mutual Funds, KB Finvest receives trailing distribution commissions paid directly by the respective Asset Management Companies (AMCs) as permitted under SEBI regulations. On Insurance products, standard statutory brokerage is paid by licensed insurance companies. On Loan facilitations, any applicable referral payout is made by the lending institution. No separate fee is charged to the client.',
                    ],
                    [
                        'heading' => 'Nomenclature & RIA Status',
                        'content' => 'KB Finvest acts as a mutual fund distributor and loan/insurance facilitator. The practice does NOT hold SEBI Registered Investment Adviser (RIA) certification and does NOT charge advisory fees.',
                    ],
                    [
                        'heading' => 'Custody & Payment Safety',
                        'content' => 'KB Finvest never handles client investment or loan funds directly. All mutual fund payments are processed directly to the AMC or through BSE Star MF / NSE NMF platforms. Loan repayments and insurance premiums must be made directly to the respective regulated institutions.',
                    ],
                ],
            ],
            'privacy' => [
                'title' => 'Privacy Notice (DPDP Compliant)',
                'sections' => [
                    [
                        'heading' => 'Collection & Purpose',
                        'content' => 'We collect personal details (name, phone number, email, city, and enquiry context) solely to respond to your specific requests for financial product distribution and loan facilitation.',
                    ],
                    [
                        'heading' => 'Data Protection & Consent',
                        'content' => 'Data is processed in compliance with the Digital Personal Data Protection (DPDP) Act, 2023. We never sell, rent, or trade your personal data. You may withdraw consent at any time by contacting our grievance officer.',
                    ],
                    [
                        'heading' => 'Calculator Privacy Guarantee',
                        'content' => 'All calculators on this website run 100% client-side inside your browser. No financial numbers, salaries, or inputs entered into calculators are transmitted to our servers or stored.',
                    ],
                ],
            ],
            'terms' => [
                'title' => 'Terms of Use',
                'sections' => [
                    [
                        'heading' => 'Information & Education Only',
                        'content' => 'The content, illustrations, and calculator outputs on this site are provided for informational and educational purposes only. They do not constitute financial, investment, legal, or tax advice.',
                    ],
                    [
                        'heading' => 'No Assured Returns or Sanctions',
                        'content' => 'Past performance of mutual funds does not guarantee future results. Loan sanctions, interest rates, and insurance policy issuances are subject to underwriting by the respective regulated institutions.',
                    ],
                    [
                        'heading' => 'Jurisdiction',
                        'content' => 'Any legal dispute arising from the use of this website shall be governed exclusively by the laws of India and subject to the jurisdiction of the competent courts in Ludhiana, Punjab.',
                    ],
                ],
            ],
            'grievance' => [
                'title' => 'Grievance Redressal Mechanism',
                'sections' => [
                    [
                        'heading' => 'Step 1: Contact KB Finvest Directly',
                        'content' => 'Reach out to Kulwinder Singh directly at +91 79734 61669 or email ks_sandhu07@yahoo.co.in. Most queries and concerns are resolved within 24–48 working hours.',
                    ],
                    [
                        'heading' => 'Step 2: Escalation to Product Providers',
                        'content' => 'For operational issues involving specific mutual fund folios, insurance claims, or loan accounts, we assist in escalating directly to the Principal Officer or Grievance Cell of the AMC, Insurer, or Bank.',
                    ],
                    [
                        'heading' => 'Step 3: Regulatory Portals',
                        'content' => 'Mutual Funds: SEBI SCORES (scores.sebi.gov.in) & AMFI. Insurance: IRDAI Bima Bharosa (bimabharosa.irdai.gov.in / Toll Free 155255). Banking/NBFC: RBI Integrated Ombudsman Scheme (rbi.org.in).',
                    ],
                ],
            ],
        ];

        return [
            'slug' => $slug,
            'currentDoc' => $docs[$slug] ?? $docs['disclosures'],
            'availableDocs' => [
                ['slug' => 'disclosures', 'title' => 'Regulatory Disclosures'],
                ['slug' => 'privacy', 'title' => 'Privacy Notice'],
                ['slug' => 'terms', 'title' => 'Terms of Use'],
                ['slug' => 'grievance', 'title' => 'Grievance Redressal'],
            ],
        ];
    }
}
