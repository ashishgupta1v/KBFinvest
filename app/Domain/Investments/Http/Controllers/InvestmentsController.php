<?php

namespace App\Domain\Investments\Http\Controllers;

use App\Domain\Investments\Services\InvestmentCatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvestmentsController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Investments/Index', [
            'data' => InvestmentCatalogService::getData(),
            'initialTab' => $request->query('tab', 'mf'),
        ]);
    }
}
