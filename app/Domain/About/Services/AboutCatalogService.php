<?php

namespace App\Domain\About\Services;

class AboutCatalogService
{
    public static function getData(): array
    {
        return [
            'aboutParagraphs' => [
                'KB Finvest is the practice of <strong>Kulwinder Singh</strong> in Central Town, Ludhiana. It exists for a simple reason: most people are sold financial products one at a time, by different people, with nobody looking at how the pieces fit together.',
                'The work here is the opposite of that. A first meeting is a conversation about your household — what comes in, who depends on it, what you owe, and what would happen if your income stopped for six months. Products come afterwards, and only where they answer a real question.',
                'Across mutual funds, professionally managed portfolios, life, health and motor insurance, and the full loan suite, Kulwinder works with the documents rather than the brochures: the scheme information document, the policy wording, the sanction letter. Those are the pages that decide what you actually receive.',
                'Clients here are salaried families building their first corpus, MSME owners in Ludhiana needing working capital or collateral-free finance, senior citizens looking for honest health cover, and business owners with accounts that have slipped and need a calm plan rather than a lecture.',
            ],
            'registrations' => [
                ['k' => 'Legal Entity', 'v' => 'KB Finvest', 'hint' => 'Registered proprietorship in Ludhiana, Punjab'],
                ['k' => 'AMFI ARN', 'v' => 'Pending Verification', 'hint' => 'AMFI Registered Mutual Fund Distributor'],
                ['k' => 'SEBI RIA', 'v' => 'Not Registered', 'hint' => 'KB Finvest does not offer fee-based investment advice'],
                ['k' => 'EUIN', 'v' => 'To be confirmed', 'hint' => 'Employee Unique Identification Number'],
                ['k' => 'Insurance Channel', 'v' => 'Licensed POSP / Corporate Intermediary', 'hint' => 'IRDAI compliant distribution'],
            ],
            'frameworks' => [
                ['b' => 'SEBI', 'd' => 'Governs mutual funds, portfolio managers and investment advisers, and restricts what a distributor may claim.', 'url' => 'https://www.sebi.gov.in/'],
                ['b' => 'AMFI', 'd' => 'Registers mutual fund distributors, issues the ARN and sets the display and conduct rules.', 'url' => 'https://www.amfiindia.com/'],
                ['b' => 'IRDAI', 'd' => 'Regulates insurers and their distribution channels, and runs the Bima Bharosa grievance portal.', 'url' => 'https://www.irdai.gov.in/'],
                ['b' => 'RBI', 'd' => 'Sets lending norms, loan-against-securities ceilings and the MSME collateral rules, and runs the Ombudsman scheme.', 'url' => 'https://www.rbi.org.in/'],
                ['b' => 'CGTMSE', 'd' => 'The Trust that guarantees collateral-free credit to micro and small enterprises through their lending institution.', 'url' => 'https://www.cgtmse.in/'],
                ['b' => 'DPDP Act', 'd' => 'Governs how your personal data is collected, stored and deleted, and gives you the right to withdraw consent.', 'url' => 'https://www.meity.gov.in/'],
            ],
            'vsTable' => [
                ['The first meeting opens with a product and a premium.', 'The first meeting opens with your income, dependants and EMIs. No product is named.'],
                ['Told what the brochure says.', 'Shown the policy wording, the scheme document and the sanction letter — the pages that decide what you receive.'],
                ['“This fund gave 22% last year.”', 'No return, ranking or “best insurer” claim is ever made. Regulators restrict them, and they predict nothing.'],
                ['How they are paid is never mentioned.', 'Commission source is published on this site, in the disclosures, before you ask.'],
                ['At claim time you are given a call-centre number.', 'Claim documents assembled with you, hospital desk and insurer followed up until it closes.'],
                ['You hear from them again at renewal season.', 'A scheduled annual review, plus reminders 30, 15 and 7 days before every renewal date.'],
                ['Calls the role “investment advisor” without registration.', 'Says “mutual fund distributor”, because that is the registration actually held.'],
            ],
            'fees' => [
                'Advice, planning and the annual review are free to you.',
                'On mutual funds, KB Finvest is paid a distribution commission by the asset management company out of the scheme — never by an extra charge to you.',
                'On insurance, the insurer pays a standard commission that is built into the premium set by the insurer, not added on top.',
                'On loans, any fee is charged by the lender; KB Finvest does not collect processing fees, deposits or EMI payments from you.',
                'We do not charge a separate advisory fee. Charging one would require SEBI Investment Adviser registration, which KB Finvest does not hold.',
            ],
            'never' => [
                'Promise a return, a sanction, an allotment or a settlement.',
                'Rank one insurer or fund as "the best" — we show you the wording and the numbers instead.',
                'Ask you to transfer money to a personal account. Payments go directly to the AMC, the insurer or the lender.',
                'Ask for your net banking password, card PIN or OTP. Nobody from KB Finvest ever will.',
                'Sell you a product you cannot explain back to us in your own words.',
            ],
        ];
    }
}
