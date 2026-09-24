<?php

namespace App\Domain\Leads\Http\Controllers;

use App\Domain\Leads\Actions\CaptureLeadAction;
use App\Domain\Leads\Data\LeadInquiryData;
use App\Domain\Leads\Http\Requests\StoreLeadInquiryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadsController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Contact/Index', [
            'initialService' => $request->query('service', 'mutual_funds'),
        ]);
    }

    public function store(StoreLeadInquiryRequest $request, CaptureLeadAction $action): RedirectResponse
    {
        $data = LeadInquiryData::fromRequest($request);

        $action->execute($data);

        return redirect()->back()->with('success', 'Thank you! Your enquiry has been received. Kulwinder Singh will review it and call or message you back.');
    }
}
