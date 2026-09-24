<?php

namespace App\Domain\Compliance\Http\Controllers;

use App\Domain\Compliance\Services\LegalCatalogService;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends Controller
{
    public function show(string $slug = 'disclosures'): Response
    {
        return Inertia::render('Legal/Show', [
            'data' => LegalCatalogService::getData($slug),
        ]);
    }
}
