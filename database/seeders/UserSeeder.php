<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin BPS — Roles: Admin (2) + User (6)
        $admin = User::updateOrCreate(
            ['username' => 'admin_bps'],
            [
                'name' => 'Admin BPS',
                'email' => 'admin@bps.go.id',
                'password' => Hash::make('ewakoadmin'),
            ]
        );
        $admin->roles()->sync([2, 6]);

        // Pengelola Barang BPS — Roles: Pengelola Barang (4) + User (6)
        $aset = User::firstOrCreate(
            ['email' => 'aset@bps.go.id'],
            [
                'username' => 'aset_bps',
                'name' => 'Pengelola Barang BPS',
                'password' => Hash::make('password'),
            ]
        );
        $aset->roles()->sync([4, 6]);

        // Pimpinan BPS — Roles: Pimpinan (1) + User (6)
        $pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan@bps.go.id'],
            [
                'username' => 'pimpinan_bps',
                'name' => 'Pimpinan BPS',
                'password' => Hash::make('password'),
            ]
        );
        $pimpinan->roles()->sync([1, 6]);
    }
}