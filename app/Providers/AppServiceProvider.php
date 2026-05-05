<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Policies\AturanTiket;
use Illuminate\Auth\Events\Login;
use App\Listeners\RecordLoginActivity;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Services\Notification\Tickets\TicketNotificationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    //Mendaftarkan layanan aplikasi.
    public function register(): void
    {
        //
    }

    //Inisialisasi layanan aplikasi (bootstrapping).
    public function boot(): void
    {
        Event::listen(Login::class, RecordLoginActivity::class);

        // Daftarkan aturan akses (Policy) untuk model Tiket
        Gate::policy(Ticket::class, AturanTiket::class);

        // View Composer for Navigation
        View::composer('layouts.navigation', function ($view) {
            $notificationService = app(TicketNotificationService::class);
            $view->with('notifications', $notificationService->getNotifikasiUntukUser());
        });
    }
}
