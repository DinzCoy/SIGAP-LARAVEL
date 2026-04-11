<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Pimpinan'],
            ['id' => 2, 'name' => 'Admin'],
            ['id' => 3, 'name' => 'Teknisi'],
            ['id' => 4, 'name' => 'Pengelola Barang'],
            ['id' => 5, 'name' => 'Pengelola Ruangan'],
            ['id' => 6, 'name' => 'User'],
        ];

        DB::table('roles')->insert($roles);
    }
}