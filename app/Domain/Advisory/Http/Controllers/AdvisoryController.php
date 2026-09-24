<?php

namespace App\Domain\Advisory\Http\Controllers;

use App\Domain\Advisory\Services\AdvisoryCatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdvisoryController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Advisory/Index', [
            'data' => AdvisoryCatalogService::getData(),
            'initialTab' => $request->query('tab', 'pe'),
        ]);
    }
}
