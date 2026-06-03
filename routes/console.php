<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Cron untuk Pengingat Peminjaman Aset (Setiap jam 08:00 pagi)
use Illuminate\Support\Facades\Schedule;
Schedule::command('loans:send-reminders')->dailyAt('08:00');
