<?php

namespace App\Providers;

use App\Domain\Consultation\Events\AppointmentScheduled;
use App\Domain\Consultation\Listeners\SendAppointmentWebhook;
use App\Domain\Leads\Events\LeadCaptured;
use App\Domain\Leads\Listeners\SendLeadWebhook;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Event::listen(
            AppointmentScheduled::class,
            SendAppointmentWebhook::class,
        );

        Event::listen(
            LeadCaptured::class,
            SendLeadWebhook::class,
        );
    }
}
