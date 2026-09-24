<?php

namespace App\Domain\About\Http\Controllers;

use App\Domain\About\Services\AboutCatalogService;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('About/Index', [
            'data' => AboutCatalogService::getData(),
        ]);
    }
}
