<?php

namespace App\Domain\Home\Services;

class HomeDataService
{
    public static function getData(): array
    {
        return [
            'proof' => [
                ['ic' => 'i-pin', 't' => 'A real office', 'd' => 'Central Town, Ludhiana. Walk in, sit down, ask anything.'],
                ['ic' => 'i-users', 't' => 'One desk, five needs', 'd' => 'Funds, PMS, market instruments, insurance and loans under one plan.'],
                ['ic' => 'i-wa', 't' => 'Answered personally', 'd' => 'Your messages reach Kulwinder, not a call centre queue.'],
                ['ic' => 'i-refresh', 't' => 'Service after the sale', 'd' => 'Renewals, claim help and portfolio reviews, every year.'],
            ],
            'figures' => [
                ['v' => '₹500', 'l' => 'The smallest monthly SIP', 'd' => 'Many schemes accept this. Starting small and stepping up beats waiting until you can commit a large amount.'],
                ['v' => '₹50 lakh', 'l' => 'Minimum investment for PMS', 'd' => 'The SEBI floor for a Portfolio Management Service. Below it, a mutual fund portfolio does the same job for less.'],
                ['v' => '₹1 crore', 'l' => 'Cap on loans against securities', 'd' => 'Per individual, across the entire banking system, under the RBI directions effective 1 July 2026.'],
                ['v' => '85%', 'l' => 'Highest loan-to-value allowed', 'd' => 'On debt mutual funds. Equity funds and ETFs reach 75 per cent; listed shares stop at 60 per cent.'],
                ['v' => '₹20 lakh', 'l' => 'Collateral-free by right', 'd' => 'Scheduled commercial banks may not demand collateral on MSE loans up to this limit — RBI Master Direction, 9 February 2026.'],
                ['v' => '₹10 crore', 'l' => 'CGTMSE guarantee ceiling', 'd' => 'The maximum cover a lender can obtain for a single borrower since 1 April 2025. You apply to the bank, never to the Trust.'],
            ],
            'figuresSrc' => 'Sources: SEBI and AMFI circulars; RBI capital-market-exposure amendment directions of 13 February 2026 (effective 1 July 2026); RBI MSME Master Direction of 9 February 2026; CGTMSE scheme guidelines. Checked 20 September 2026.',
            'pillars' => [
                ['ic' => 'i-growth', 't' => 'Mutual Funds', 'd' => 'SIPs and lump sums arranged around your goals, reviewed every year, with the paperwork done properly the first time.', 'url' => '/investments?tab=mf'],
                ['ic' => 'i-pie', 't' => 'PMS Introductions', 'd' => 'For portfolios of ₹50 lakh and above — introductions to SEBI-registered portfolio managers, with the disclosure document explained.', 'url' => '/investments?tab=pms'],
                ['ic' => 'i-cert', 't' => 'Shares, ETFs and Bonds', 'd' => 'Understand what you are buying: equity, index ETFs, government and corporate bonds, and where each one fits.', 'url' => '/investments?tab=mkt'],
                ['ic' => 'i-shield', 't' => 'Insurance', 'd' => 'Health, life and motor cover selected on the wording, not the brochure — and supported when you have to claim.', 'url' => '/insurance?tab=health'],
                ['ic' => 'i-coin', 't' => 'Loans and Business Finance', 'd' => 'Home, property, business, working capital, loans against securities and collateral-free CGTMSE funding.', 'url' => '/loans?tab=lap'],
                ['ic' => 'i-scale', 't' => 'Specialist Advisory', 'd' => 'Pre-IPO and unlisted opportunities for informed investors, and confidential support on stressed accounts.', 'url' => '/advisory?tab=pe'],
            ],
            'flow' => [
                ['ic' => 'i-users', 't' => 'You', 'd' => 'Your bank account, your PAN, your signature. Payment leaves your account only when you authorise it.'],
                ['ic' => 'i-shield', 't' => 'KB Finvest', 'd' => 'Advice, comparison, paperwork, follow-up and claim support. No funds, no custody, no payments — ever.', 'mid' => true],
                ['ic' => 'i-bank', 't' => 'The Institution', 'd' => 'The asset management company, insurer, portfolio manager or lender. They hold the money and issue the contract.'],
            ],
            'flowNote' => [
                't' => 'Nobody from KB Finvest will ever ask you to transfer money to a personal account.',
                'd' => 'Mutual fund payments go to the AMC. Premiums go to the insurer. Loan repayments go to the lender. If anyone claiming to represent us asks otherwise, end the conversation and call the office number published on this site.',
            ],
            'safety' => [
                ['ic' => 'i-lock', 't' => 'No OTP, ever', 'd' => 'We will never ask for a one-time password, net banking password, card PIN or CVV. There is no legitimate reason for us to have them.'],
                ['ic' => 'i-doc', 't' => 'Everything in your name', 'd' => 'Folios, policies and demat holdings are opened in your name, with your nomination. You receive the statements directly.'],
                ['ic' => 'i-scale', 't' => 'Commission disclosed', 'd' => 'What we earn and who pays it is published in the disclosures, before you ask. No advisory fee is charged to you.'],
                ['ic' => 'i-refresh', 't' => 'Leave whenever you like', 'd' => 'You can change distributor, stop a SIP or port a policy at any time. Nothing here locks you to us.'],
            ],
        ];
    }
}
