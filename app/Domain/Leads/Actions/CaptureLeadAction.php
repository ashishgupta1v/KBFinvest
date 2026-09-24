<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\Data\LeadInquiryData;
use App\Domain\Leads\Events\LeadCaptured;
use App\Domain\Leads\Models\LeadInquiry;
use Illuminate\Support\Facades\Log;

class CaptureLeadAction
{
    public function execute(LeadInquiryData $data): LeadInquiry
    {
        $lead = LeadInquiry::create($data->toArray());

        LeadCaptured::dispatch($lead);

        Log::info('leads.inquiry.captured', [
            'lead_id' => $lead->id,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'city' => $lead->city,
            'service_type' => $lead->service_type,
            'ip_address' => $lead->ip_address,
        ]);

        return $lead;
    }
}
