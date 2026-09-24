<?php

namespace App\Domain\Loans\Http\Controllers;

use App\Domain\Loans\Services\LoanCatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoansController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Loans/Index', [
            'data' => LoanCatalogService::getData(),
            'initialTab' => $request->query('tab', 'lap'),
        ]);
    }
}
