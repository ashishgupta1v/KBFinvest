<?php

namespace App\Domain\Advisory\Services;

class AdvisoryCatalogService
{
    public static function getData(): array
    {
        return [
            'services' => [
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
