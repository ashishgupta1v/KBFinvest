<?php

namespace App\Domain\Insurance\Http\Controllers;

use App\Domain\Insurance\Services\InsuranceCatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InsuranceController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Insurance/Index', [
            'data' => InsuranceCatalogService::getData(),
            'initialTab' => $request->query('tab', 'health'),
        ]);
    }
}
