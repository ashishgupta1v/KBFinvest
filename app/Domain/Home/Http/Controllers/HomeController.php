<?php

namespace App\Domain\Home\Http\Controllers;

use App\Domain\Home\Services\HomeDataService;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home/Index', [
            'data' => HomeDataService::getData(),
        ]);
    }
}
