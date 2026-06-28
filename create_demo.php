<?php
$user = \App\Models\User::firstOrCreate(
    ['email' => 'demo@bps.go.id'],
    [
        'name' => 'Akun Demo (Audiens)',
        'username' => 'demo_audiens',
        'password' => bcrypt('password'),
    ]
);
$user->roles()->syncWithoutDetaching([\App\Models\User::ROLE_ADMIN]);
echo "User demo ready!\n";
