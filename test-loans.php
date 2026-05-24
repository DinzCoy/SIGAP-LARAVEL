<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetLoan;

echo "--- DISETUJUI ---\n";
$disetujui = AssetLoan::whereIn('status', [AssetLoan::STATUS_ACTIVE])->get();
foreach ($disetujui as $l) {
    echo "ID: {$l->id} | Status: {$l->status}\n";
}

echo "\n--- SELESAI ---\n";
$selesai = AssetLoan::whereIn('status', [AssetLoan::STATUS_RETURNED, AssetLoan::STATUS_REJECTED])->get();
foreach ($selesai as $l) {
    echo "ID: {$l->id} | Status: {$l->status}\n";
}
