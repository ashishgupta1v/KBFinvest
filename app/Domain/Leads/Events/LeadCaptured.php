<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\LeadInquiry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadCaptured
{
    use Dispatchable, SerializesModels;

    public function __construct(public LeadInquiry $lead) {}
}
