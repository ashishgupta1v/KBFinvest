<?php

use App\Domain\About\Http\Controllers\AboutController;
use App\Domain\Advisory\Http\Controllers\AdvisoryController;
use App\Domain\Calculators\Http\Controllers\CalculatorsController;
use App\Domain\Compliance\Http\Controllers\LegalController;
use App\Domain\Consultation\Http\Controllers\ConsultationController;
use App\Domain\Home\Http\Controllers\HomeController;
use App\Domain\Insurance\Http\Controllers\InsuranceController;
use App\Domain\Investments\Http\Controllers\InvestmentsController;
use App\Domain\Leads\Http\Controllers\LeadsController;
use App\Domain\Loans\Http\Controllers\LoansController;
use App\Domain\Resources\Http\Controllers\ResourcesController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Core Service Domains
Route::get('/investments', [InvestmentsController::class, 'index'])->name('investments');
Route::get('/insurance', [InsuranceController::class, 'index'])->name('insurance');
Route::get('/loans', [LoansController::class, 'index'])->name('loans');
Route::get('/advisory', [AdvisoryController::class, 'index'])->name('advisory');

// Interactive Calculators
Route::get('/calculators', [CalculatorsController::class, 'index'])->name('calculators');

// Resources & Knowledge Base
Route::get('/resources', [ResourcesController::class, 'index'])->name('resources');

// About & Credentials
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Book Appointment
Route::get('/book', [ConsultationController::class, 'index'])->name('book');
Route::post('/book', [ConsultationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('book.store');

// Contact & Leads
Route::get('/contact', [LeadsController::class, 'index'])->name('contact');
Route::post('/contact', [LeadsController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');

// Compliance & Legal
Route::get('/legal/{slug?}', [LegalController::class, 'show'])->name('legal.show');
