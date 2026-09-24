<?php

namespace App\Domain\Consultation\Http\Controllers;

use App\Domain\Consultation\Actions\CreateAppointmentAction;
use App\Domain\Consultation\Data\AppointmentData;
use App\Domain\Consultation\Http\Requests\StoreAppointmentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConsultationController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Book/Index', [
            'initialTopic' => $request->query('topic', 'General Financial Planning'),
        ]);
    }

    public function store(StoreAppointmentRequest $request, CreateAppointmentAction $action): RedirectResponse
    {
        $data = AppointmentData::fromRequest($request);

        $action->execute($data);

        return redirect()->back()->with('success', 'Your 30-minute consultation has been scheduled! We will confirm on WhatsApp shortly.');
    }
}
