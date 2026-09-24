<?php

namespace App\Domain\Resources\Http\Controllers;

use App\Domain\Resources\Services\ResourceCatalogService;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ResourcesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Resources/Index', [
            'data' => ResourceCatalogService::getData(),
        ]);
    }
}
