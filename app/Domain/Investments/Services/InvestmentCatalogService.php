<?php

namespace App\Domain\Investments\Services;

class InvestmentCatalogService
{
    public static function getData(): array
    {
        $mfRisk = 'Mutual Fund investments are subject to market risks, read all scheme related documents carefully. Past performance does not indicate future results.';

        return [
            'services' => [
                [
                    'id' => 'mf',
                    'ic' => 'i-growth',
                    'title' => 'Mutual Funds',
                    'tag' => 'SIP · Lump sum · Reviews',
                    'lead' => 'A disciplined way to put monthly savings to work across equity, debt and hybrid schemes — with the goal written down before the first instalment leaves your account.',
                    'who' => [
                        'First-time investors who want to start small and automate it',
                        'Salaried families saving for a house, education or retirement',
                        'Business owners parking surplus in liquid and short-duration funds',
                        'Anyone holding a drawer full of old folios with no idea what they own',
                    ],
                    'what' => [
                        'Goal mapping: We fix the amount and the date first — school fee in 2034, retirement in 2041 — then choose the category that suits that horizon.',
                        'Category, then scheme: Equity for long horizons, debt for near-term needs, hybrid where you want a smoother ride. Scheme selection follows the mandate, never a league table.',
                        'SIP, STP and SWP set-up: Monthly investing, staggered deployment of a lump sum, or a monthly withdrawal in retirement.',
                        'Consolidation: Old folios traced, nominations added, contact details corrected, statements merged so you can finally see one number.',
                        'Annual review: A yearly sit-down against the goal, plus rebalancing when the mix drifts.',
                    ],
                    'docs' => [
                        'PAN card',
                        'Aadhaar (address proof)',
                        'Cancelled cheque or bank statement',
                        'Passport-size photograph',
                        'Completed CKYC / KRA formalities',
                        'Nominee details — name, relationship, date of birth',
                    ],
                    'steps' => [
                        'Goal and risk conversation',
                        'Written plan with amounts and dates',
                        'KYC completion with the RTA / AMC',
                        'Folio creation and mandate registration',
                        'First SIP or lump sum invested',
                        'Statements to your email; annual review scheduled',
                    ],
                    'facts' => [
                        ['Start from', '₹500 per month'],
                        ['Typical horizon', '3 years or more for equity'],
                        ['Exit load', 'Scheme specific — shown before you invest'],
                        ['Who holds your money', 'The AMC, never KB Finvest'],
                    ],
                    'note' => 'We will not tell you which scheme "gave the best return last year". Selection is explained against the scheme information document, the mandate and the cost — and you keep that explanation in writing.',
                    'disc' => $mfRisk,
                ],
                [
                    'id' => 'pms',
                    'ic' => 'i-pie',
                    'title' => 'PMS Introductions',
                    'tag' => '₹50 lakh minimum',
                    'lead' => 'Portfolio Management Services are run by SEBI-registered portfolio managers who hold securities in your own demat account and manage them under a signed agreement. KB Finvest introduces you; it does not manage portfolios.',
                    'who' => [
                        'Investors with ₹50 lakh or more to commit to a single strategy',
                        'Families who already hold a mature mutual fund portfolio and want a concentrated mandate',
                        'Business owners seeking a discretionary manager with direct reporting',
                    ],
                    'what' => [
                        'What PMS is: A discretionary or non-discretionary mandate. Securities sit in your name, in your demat account, and you see every trade.',
                        'The regulatory minimum is ₹50 lakh per investor under SEBI rules. Below that, a well-built mutual fund portfolio does the same job at lower cost.',
                        'The Disclosure Document: Before you sign anything, the portfolio manager must give you a disclosure document covering strategy, risk, fees and past performance. We go through it with you line by line.',
                        'Fee structures: Fixed fee, performance fee with a hurdle, or a blend. We compare the total cost of ownership across the options you are considering.',
                        'Direct on-boarding exists: SEBI requires portfolio managers to offer a direct option without distribution charges. We will tell you when that is the cheaper route for you.',
                    ],
                    'docs' => [
                        'PAN and Aadhaar',
                        'Demat account details',
                        'Bank proof and cancelled cheque',
                        'Proof of source of funds',
                        'Signed PMS agreement and power of attorney with the portfolio manager',
                    ],
                    'steps' => [
                        'Suitability conversation and ₹50 lakh confirmation',
                        'Shortlist of registered portfolio managers and strategies',
                        'Disclosure document walkthrough',
                        'Direct meeting with the portfolio manager',
                        'Account opening and funding, directly with the manager',
                        'Quarterly reporting reviewed with you',
                    ],
                    'facts' => [
                        ['Regulatory minimum', '₹50,00,000'],
                        ['Securities held in', 'Your own demat account'],
                        ['Agreement with', 'The portfolio manager, not KB Finvest'],
                        ['Distributor registration', 'APMI (APRN) — see disclosures'],
                    ],
                    'note' => 'PMS distribution requires APMI registration in addition to NISM certification. Our registration number is published in the disclosures section and on every PMS communication.',
                    'disc' => 'PMS investments are subject to market risk. There is no assurance or guarantee of returns. Read the Disclosure Document issued by the portfolio manager before investing. KB Finvest does not manage portfolios and does not accept funds for investment.',
                ],
                [
                    'id' => 'bonds',
                    'ic' => 'i-cert',
                    'title' => 'Bonds & Fixed Income',
                    'tag' => 'Capital Preservation · Fixed Returns',
                    'lead' => 'High-safety fixed income instruments for predictable cash flow and tax efficiency — Sovereign Gold Bonds (SGB), RBI Floating Rate Savings Bonds, Section 54EC Capital Gains Bonds, and Corporate NCDs.',
                    'who' => [
                        'Retirees and conservative investors seeking stable, predictable quarterly or semi-annual income',
                        'Property sellers looking to save long-term capital gains tax under Section 54EC',
                        'Investors wanting sovereign-backed alternatives to fixed deposits with superior safety and yields',
                        'Gold investors wanting 2.5% annual interest + market gold price appreciation via Sovereign Gold Bonds',
                    ],
                    'what' => [
                        'RBI Floating Rate Savings Bonds (FRSB): 100% sovereign government-backed with interest linked to NSC rates plus a spread, paid semi-annually.',
                        '54EC Capital Gains Exemption Bonds: Lock in gains from real estate sales into REC, PFC, IRFC, or NHAI to claim full exemption under Section 54EC.',
                        'Sovereign Gold Bonds (SGB): RBI-issued gold investment with 2.50% annual coupon paid semi-annually and tax-free capital gains at maturity.',
                        'High-Grade Corporate NCDs: Secured Non-Convertible Debentures from AAA/AA+ rated institutions offering higher fixed coupon yields than standard bank deposits.',
                    ],
                    'docs' => [
                        'PAN Card and Aadhaar Card',
                        'Cancelled Cheque / Bank details for direct interest credit',
                        'Demat account details (if opting for demat holding mode)',
                        'Copy of real estate sale deed (for Section 54EC capital gains bond applications)',
                    ],
                    'steps' => [
                        'Cash flow timeline and tax bracket evaluation',
                        'Instrument selection (Sovereign vs 54EC vs Corporate NCD)',
                        'Application and allotment paperwork completed cleanly',
                        'Direct allotment confirmation and ECS mandate mapped for coupon payouts',
                    ],
                    'facts' => [
                        ['Sovereign Safety', '100% backed by Government of India on RBI bonds & SGB'],
                        ['Section 54EC', 'Up to ₹50 lakh capital gains tax exemption per financial year'],
                        ['SGB Holding', '8-year maturity with early exit window from 5th year'],
                        ['Payout Mode', 'Direct ECS credit into investor bank account'],
                    ],
                    'note' => 'Bond allotments and certificates are issued directly by the Reserve Bank of India or the designated public/corporate issuer. KB Finvest acts strictly as an authorized facilitator and never accepts client investment funds in its own name.',
                    'disc' => 'Fixed income securities carry issuer credit risk, reinvestment risk, and liquidity risk. Tax exemptions are subject to prevailing Income Tax laws and individual assessment.',
                ],
                [
                    'id' => 'mkt',
                    'ic' => 'i-trend',
                    'title' => 'Shares & Index ETFs',
                    'tag' => 'Understand before you buy',
                    'lead' => 'Direct market instruments explained without the noise — what an ETF actually tracks, index weightages, and how demat hygiene safeguards your family holdings.',
                    'who' => [
                        'Investors already holding a demat account who want an unbiased portfolio audit',
                        'People who want low-cost index and passive exchange-traded fund (ETF) exposure',
                        'Families needing assistance with IEPF share recovery, physical share dematerialization, and transmission paperwork',
                    ],
                    'what' => [
                        'Equity basics done properly: Ownership, dilution, promoter pledging, and why price and value are different things.',
                        'ETFs and index funds: What the index contains, how tracking error appears, and how exchange-traded units differ from open-ended mutual funds.',
                        'Demat hygiene: Nomination, consolidation of legacy physical shares, transmission paperwork, and IEPF claim support for forgotten dividends.',
                        'Tax and record keeping: Annual Information Statement (AIS) reconciliation and capital gains statement collation for your CA.',
                    ],
                    'docs' => [
                        'PAN and Aadhaar',
                        'Existing demat and trading account statements',
                        'Bank proof for pay-in and pay-out mapping',
                    ],
                    'steps' => [
                        'What you already hold, reviewed',
                        'Where direct instruments fit — and where they do not',
                        'Account and nomination clean-up',
                        'Ongoing statements and annual review',
                    ],
                    'facts' => [
                        ['Best suited to', 'Investors who will read a factsheet'],
                        ['Typical horizon', '5 years or more for equity'],
                        ['Our role', 'Education and facilitation'],
                    ],
                    'note' => 'Our role here is education and facilitation. Trading and demat accounts are opened with, and securities are held by, a SEBI-registered broker and depository participant — not by KB Finvest.',
                    'disc' => 'Securities markets carry risk including loss of principal. Nothing on this page is a recommendation to buy or sell any specific security. Please read all offer documents carefully.',
                ],
            ],
            'ladder' => [
                ['nm' => 'Liquid and overnight funds', 'hz' => 'Days to 6 months · parking money you will need soon', 'risk' => 10],
                ['nm' => 'Ultra-short and low-duration debt', 'hz' => '6 months to 2 years · emergency fund, near-term goals', 'risk' => 25],
                ['nm' => 'Short and medium duration debt & Bonds', 'hz' => '2 to 4 years · goals with a fixed date', 'risk' => 40],
                ['nm' => 'Hybrid and balanced advantage', 'hz' => '3 to 5 years · a smoother ride into equity', 'risk' => 55],
                ['nm' => 'Large-cap and index equity', 'hz' => '5 years and beyond · core long-term growth', 'risk' => 72],
                ['nm' => 'Mid, small-cap and sectoral equity', 'hz' => '7 years and beyond · satellite, high volatility', 'risk' => 88],
                ['nm' => 'Unlisted, pre-IPO and private equity', 'hz' => 'Undefined · illiquid, may lose everything', 'risk' => 100],
            ],
            'onboarding' => [
                'url' => 'https://p.njw.bz/11786',
                'title' => 'Start Your Mutual Fund Portfolio 100% Online',
                'subtitle' => 'Paperless e-KYC in 5 minutes via NJ Wealth Partner Network · AMFI ARN-178400',
                'badge' => 'Instant Digital Onboarding',
                'partnerCode' => '11786',
                'partnerName' => 'NJ Wealth Partner Network',
                'features' => [
                    [
                        't' => '100% Paperless Aadhaar e-KYC',
                        'd' => 'Zero physical paperwork. Complete instant video/Aadhaar OTP verification from your smartphone.',
                        'ic' => 'i-shield',
                    ],
                    [
                        't' => 'Direct Access to 40+ AMCs',
                        'd' => 'Invest across HDFC, SBI, ICICI Prudential, Nippon, Kotak, Axis, Tata, DSP, and all major Indian fund houses.',
                        'ic' => 'i-bank',
                    ],
                    [
                        't' => 'Instant SIP & Auto-Pay Mandate',
                        'd' => 'Activate automated monthly SIPs starting from ₹500 via NetBanking or UPI mandate in minutes.',
                        'ic' => 'i-growth',
                    ],
                    [
                        't' => '24/7 Mobile App Portfolio Tracking',
                        'd' => 'Live NAV updates, capital gains statements, family portfolio consolidation, and mobile app access anytime.',
                        'ic' => 'i-trend',
                    ],
                ],
            ],
        ];
    }
}
