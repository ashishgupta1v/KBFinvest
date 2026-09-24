<?php

namespace App\Providers;

use App\Domain\Consultation\Events\AppointmentScheduled;
use App\Domain\Consultation\Listeners\SendAppointmentWebhook;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            AppointmentScheduled::class,
            SendAppointmentWebhook::class,
        );
    }
}
