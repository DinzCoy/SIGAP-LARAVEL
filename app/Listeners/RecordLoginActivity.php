<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Login;

class RecordLoginActivity
{
    public function handle(Login $event): void
    {
        LoginActivity::create([
            'user_id'      => $event->user->id,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}
