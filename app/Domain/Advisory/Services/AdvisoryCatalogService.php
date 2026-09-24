<?php

namespace App\Domain\Advisory\Services;

class AdvisoryCatalogService
{
    public static function getData(): array
    {
        return [
            'services' => [
                [
                    'id' => 'pe',
                    'ic' => 'i-rocket',
                    'title' => 'Private Equity, Pre-IPO and IPO',
                    'tag' => 'High risk · Illiquid',
                    'lead' => 'Access to unlisted and pre-IPO shares, and help navigating the IPO process. This is the highest-risk corner of the practice and we treat it that way.',
                    'who' => [
                        'Investors who already hold a complete, diversified core portfolio',
                        'People who can leave this money untouched for years',
                        'Business owners familiar with private company risk',
                    ],
                    'what' => [
                        'Unlisted and pre-IPO shares: How they are bought and transferred, how price discovery works without an exchange, and why the spread is wide.',
                        'Valuation without a market price: What you can and cannot verify from the information a private company actually discloses.',
                        'The listing question: An IPO may be delayed indefinitely or never happen. Your exit may be a secondary sale at a price you do not control.',
                        'Lock-ins and restrictions: Pre-IPO holdings frequently carry a post-listing lock-in. Plan your liquidity around that, not around the listing date.',
                        'IPO applications: ASBA, UPI mandates, category selection, and the plain reality that allotment in a popular issue is a lottery.',
                        'Position sizing: Our standing view is that this is a small satellite allocation you could afford to lose entirely.',
                    ],
                    'docs' => [
                        'PAN, Aadhaar and full KYC',
                        'Demat account for off-market transfer',
                        'Proof of source of funds',
                        'Signed risk acknowledgement',
                    ],
                    'steps' => [
                        'Suitability and risk-tolerance conversation',
                        'Written risk disclosure, acknowledged by you',
                        'Opportunity and documentation reviewed together',
                        'Transaction executed directly with the counterparty',
                        'Holdings tracked; exit options reviewed periodically',
                    ],
                    'facts' => [
                        ['Liquidity', 'Low to none until listing'],
                        ['Price discovery', 'Negotiated, not exchange-traded'],
                        ['Lock-in', 'Often applies after listing'],
                        ['Suggested allocation', 'A small satellite, not a core holding'],
                    ],
                    'note' => 'There is no assurance of listing, of allotment, of a listing gain, or of any exit at all. Anyone promising you those things about an unlisted share is not someone you should transact with.',
                    'disc' => 'Investments in unlisted and pre-IPO securities are speculative, illiquid and may result in total loss of capital. No return, listing or exit is assured. This section is educational and is not a recommendation or an offer to buy or sell any security.',
                ],
                [
                    'id' => 'stress',
                    'ic' => 'i-scale',
                    'title' => 'Stressed Account Handling',
                    'tag' => 'Confidential · No judgement',
                    'lead' => 'Missed EMIs, an SMA tag, a recovery notice, an account classified as NPA. It is a situation, not a verdict — and it almost always has more options than you are being told.',
                    'who' => [
                        'Businesses whose accounts slipped after a demand shock or a receivable failure',
                        'Individuals facing recovery action on a home or business loan',
                        'Guarantors who have received a notice',
                        'Anyone being pressured into a settlement they do not understand',
                    ],
                    'what' => [
                        'Understand where you actually stand: Special Mention Account stages, the 90-day NPA line, and what each one triggers.',
                        'Restructuring: Revised tenure, a moratorium, or re-phased instalments — where the business is viable and the problem is timing.',
                        'One-time settlement: How lenders evaluate an OTS proposal, what a realistic offer looks like, and what an OTS does to your credit record afterwards.',
                        'Notices and proceedings: Plain-language explanation of recovery notices, security-enforcement steps and tribunal routes, so you know what a document means before the deadline passes.',
                        'Preparing the file: Cash-flow reality, the source of settlement funds, and a proposal a credit committee can actually approve.',
                        'Working with your lawyer: Where legal representation is required we work alongside your advocate. We do not provide legal representation.',
                    ],
                    'docs' => [
                        'All notices and correspondence received',
                        'Sanction letters and loan agreements',
                        'Account statements showing the default period',
                        'Current financials and cash-flow position',
                        'Details of security offered and any guarantors',
                    ],
                    'steps' => [
                        'Confidential conversation — no financial detail needed to start',
                        'Position established from the documents',
                        'Options set out honestly, including the ones you will not like',
                        'Proposal prepared and presented to the lender',
                        'Negotiation supported through to a written outcome',
                        'Credit-record rehabilitation plan afterwards',
                    ],
                    'facts' => [
                        ['First meeting', 'Confidential, no obligation'],
                        ['Our role', 'Advisory and representation support'],
                        ['Legal work', 'Handled by your advocate'],
                        ['Outcome decided by', 'The lender and the courts'],
                    ],
                    'note' => 'We cannot and will not promise a settlement, a waiver or a specific outcome. Anyone who does is selling you something. What we can do is make sure your position is presented properly and that you understand every document before you sign it.',
                    'disc' => 'This is advisory and representation support, not legal advice or legal representation. Outcomes rest entirely with the lender, the relevant authority and the courts. No settlement, waiver or restructuring is assured.',
                ],
            ],
        ];
    }
}
