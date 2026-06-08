<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $asset = \App\Models\Asset::first();
    $user = \App\Models\User::first();
    $service = app(\App\Services\Assets\AssetService::class);
    $result = $service->requestTransfer($asset, $user, 'Testing Alasan Mutasi Permanen Minimal 10');
    echo "Success: " . json_encode($result);
} catch (\Throwable $e) {
    echo "Error: " . get_class($e) . ": " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
