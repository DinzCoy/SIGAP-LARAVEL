<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'api_key', 'value' => 'BPS-SULSEL-SECRET-2026', 'description' => 'API Key untuk autentikasi agent'],
            ['key' => 'server_url', 'value' => 'http://192.168.20.24', 'description' => 'URL utama server'],
            ['key' => 'ram_threshold', 'value' => '90', 'description' => 'Batas persen RAM untuk anomali'],
            ['key' => 'disk_threshold_gb', 'value' => '10', 'description' => 'Batas sisa disk (GB) untuk alert'],
            ['key' => 'report_interval_days', 'value' => '7', 'description' => 'Interval laporan rutin (hari)'],
            ['key' => 'log_retention_months', 'value' => '6', 'description' => 'Lama penyimpanan log (bulan)'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
