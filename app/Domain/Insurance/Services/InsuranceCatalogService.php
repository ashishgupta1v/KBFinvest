<?php

namespace App\Domain\Insurance\Services;

class InsuranceCatalogService
{
    public static function getData(): array
    {
        $insLine = 'Insurance is the subject matter of solicitation. Benefits, exclusions and waiting periods are governed entirely by the policy wording issued by the insurer.';

        return [
            'services' => [
                [
                    'id' => 'health',
                    'ic' => 'i-heart',
                    'title' => 'Health Insurance',
                    'tag' => 'Family · Senior citizen · Top-up',
                    'lead' => 'A hospital bill is the fastest way to undo ten years of saving. The right cover is decided by the wording — room rent, waiting periods, co-pay and sub-limits — not by the premium on the front page.',
                    'who' => [
                        'Families who currently rely only on an employer group policy',
                        'Parents adding senior citizens to their cover',
                        'Diabetics and hypertensives worried about pre-existing disease waiting',
                        'Anyone whose premium jumped at renewal and wants to know why',
                    ],
                    'what' => [
                        'Cover adequacy: Sum insured judged against your city, your family size and what a two-day ICU stay costs locally — not a round number picked from a brochure.',
                        'The five clauses that decide a claim: Room-rent limit, pre-existing disease waiting period, specific-illness waiting, co-payment, and disease-wise sub-limits. We read all five with you before you sign.',
                        'Base plus top-up: Often a smaller base cover with a super top-up gives you a far larger total for the same money. We show the arithmetic both ways.',
                        'Restore, no-claim bonus and day-care: What refills your sum insured mid-year, what grows it each claim-free year, and which procedures no longer need 24-hour admission.',
                        'Portability: You can move insurer at renewal and carry your accumulated waiting periods with you. We handle the paperwork and the timing.',
                        'Claim support: Cashless pre-authorisation or reimbursement — documents assembled, hospital desk followed up, queries answered.',
                    ],
                    'docs' => [
                        'Proposal form and KYC (PAN, Aadhaar)',
                        'Medical history and current prescriptions',
                        'Previous policy copy, for portability',
                        'Recent test reports where the insurer asks for them',
                    ],
                    'steps' => [
                        'Gap analysis — who is covered, for how much, with what gaps',
                        'Shortlist compared on wording, not brochure',
                        'Proposal, disclosure of health history, medicals if required',
                        'Policy issued; free-look period explained',
                        'Renewal reminders at 30, 15 and 7 days',
                        'Claim assistance whenever you need it',
                    ],
                    'facts' => [
                        ['Free-look period', 'As stated in your policy schedule'],
                        ['Renewal', 'Lifelong, per IRDAI norms'],
                        ['Portability window', 'Apply 30–60 days before renewal'],
                        ['Claim route', 'Cashless or reimbursement'],
                    ],
                    'note' => 'Disclose every existing condition on the proposal form, even if someone tells you otherwise. Non-disclosure is the single most common reason a health claim is refused, and no amount of follow-up fixes it afterwards.',
                    'disc' => $insLine,
                ],
                [
                    'id' => 'life',
                    'ic' => 'i-family',
                    'title' => 'Life Insurance',
                    'tag' => 'Term · Savings · Retirement',
                    'lead' => 'Life cover has one job: if your income stops, your family carries on. We separate that job from investment, price both honestly, and let you decide.',
                    'who' => [
                        'Anyone with dependants, a home loan or a business liability',
                        'New parents',
                        'Business owners with a partnership or key-person exposure',
                        'People holding several small endowment policies and unsure what they are worth',
                    ],
                    'what' => [
                        'How much cover: Human Life Value — income to replace, liabilities to clear, goals to fund, less what you already hold. Our HLV calculator runs the same arithmetic.',
                        'Term plans: The cheapest way to buy a large sum assured. Level cover, increasing cover, or cover that runs to age 60, 65 or beyond.',
                        'Savings and endowment plans: Guaranteed maturity in exchange for a far smaller sum assured. We show the internal rate of return so you can compare it honestly with a debt fund.',
                        'ULIPs: Insurance plus market-linked investment in one contract, with a five-year lock-in. We show every charge before you consider one.',
                        'Riders: Critical illness, accidental death, waiver of premium, income benefit — and which of them you genuinely need.',
                        'Nomination and the MWP Act: Correct nominee details, and where an MWP Act endorsement protects the proceeds for your spouse and children from creditors.',
                    ],
                    'docs' => [
                        'PAN, Aadhaar and address proof',
                        'Income proof — ITR, Form 16 or salary slips',
                        'Medical examination, arranged by the insurer',
                        'Existing policy details',
                        'Bank details for the premium mandate',
                    ],
                    'steps' => [
                        'HLV calculation and an honest cover target',
                        'Term versus savings comparison, in writing',
                        'Proposal with full health and income disclosure',
                        'Medicals and underwriting',
                        'Policy issued; nomination confirmed',
                        'Reviewed at every life event — marriage, child, new loan',
                    ],
                    'facts' => [
                        ['Typical term cover', 'To age 60–65'],
                        ['Free-look', 'As per policy schedule'],
                        ['Claim support', 'Provided to your nominee'],
                        ['Premium paid to', 'The insurer, directly'],
                    ],
                    'note' => 'We publish no claim-settlement percentages here. Ratios differ by number and by amount, and the figures circulating on comparison sites disagree with each other. Ask us and we will show you the current IRDAI Annual Report table, with the financial year stated.',
                    'disc' => $insLine.' Tax benefits are subject to prevailing tax law and may change.',
                ],
                [
                    'id' => 'motor',
                    'ic' => 'i-car',
                    'title' => 'Motor Insurance',
                    'tag' => 'Two-wheeler · Car · Commercial',
                    'lead' => 'Third-party cover is compulsory by law. Own-damage cover is what actually saves you money — provided the IDV is right and the add-ons match how you use the vehicle.',
                    'who' => [
                        'Car and two-wheeler owners at renewal',
                        'Fleet and commercial vehicle operators',
                        'Buyers of a used vehicle transferring an existing policy',
                        'Anyone who let a policy lapse and now needs an inspection',
                    ],
                    'what' => [
                        'IDV, explained: Insured Declared Value is the manufacturer listed price less depreciation — and it is the ceiling on a total-loss claim. Under-declaring to save premium costs you at exactly the wrong moment.',
                        'Third-party versus comprehensive: What the law requires, and what it leaves you paying for yourself.',
                        'Add-ons worth the money: Zero depreciation, engine protection, return to invoice, consumables, roadside assistance, key replacement, tyre cover — matched to the age and use of the vehicle.',
                        'No Claim Bonus: It belongs to you, not the vehicle. It can be carried to your next car and recovered if it was wrongly denied. We retrieve it at transfer.',
                        'Commercial vehicles: Goods carrying, passenger carrying and miscellaneous classes, with permit, fitness and driver liability requirements covered.',
                        'Claims: Intimation timelines, surveyor visit, cashless garages, salvage and total-loss settlement.',
                    ],
                    'docs' => [
                        'Registration Certificate (RC)',
                        'Previous policy copy',
                        'Driving licence',
                        'Vehicle inspection, if the policy has lapsed',
                        'Loan or hypothecation details, if any',
                    ],
                    'steps' => [
                        'Vehicle and usage details captured',
                        'IDV and add-on options placed side by side',
                        'Quote arranged with the insurer',
                        'Policy issued before expiry, never after',
                        'Renewal reminder every year',
                        'Claim intimation and follow-up support',
                    ],
                    'facts' => [
                        ['Third-party cover', 'Mandatory under the Motor Vehicles Act'],
                        ['NCB', 'Belongs to the owner, transferable'],
                        ['Lapsed policy', 'Needs inspection before renewal'],
                        ['Claim intimation', 'Immediately — same day where possible'],
                    ],
                    'note' => 'Never let motor cover lapse, even by a day. Driving uninsured is an offence, you carry the full third-party liability yourself, and your accumulated No Claim Bonus is lost after a 90-day break.',
                    'disc' => $insLine,
                ],
            ],
            'insurers' => [
                'health' => [
                    'label' => 'Health Insurance Partners',
                    'list' => [
                        'Care Health Insurance',
                        'HDFC ERGO General Insurance',
                        'TATA AIG General Insurance',
                        'ManipalCigna Health Insurance',
                        'SBI General Insurance',
                        'Star Health and Allied Insurance',
                        'Niva Bupa Health Insurance',
                    ],
                ],
                'life' => [
                    'label' => 'Life Insurance Partners',
                    'list' => [
                        'ICICI Prudential Life Insurance',
                        'HDFC Life Insurance',
                        'Tata AIA Life Insurance',
                        'Bajaj Life Insurance',
                        'Axis Max Life Insurance',
                    ],
                ],
                'motor' => [
                    'label' => 'Motor & General Insurance Partners',
                    'list' => [
                        'TATA AIG General Insurance',
                        'ICICI Lombard General Insurance',
                        'HDFC ERGO General Insurance',
                        'Bajaj General Insurance',
                        'IFFCO Tokio General Insurance',
                        'Go Digit General Insurance',
                        'Royal Sundaram General Insurance',
                    ],
                ],
            ],
        ];
    }
}
