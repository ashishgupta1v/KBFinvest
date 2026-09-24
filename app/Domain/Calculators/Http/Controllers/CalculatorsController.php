<?php

namespace App\Domain\Calculators\Http\Controllers;

use App\Domain\Calculators\Services\CalculatorCatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalculatorsController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Calculators/Index', [
            'data' => CalculatorCatalogService::getData(),
            'initialCalc' => $request->query('calc', 'sip'),
        ]);
    }
}
