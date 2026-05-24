<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetLoan;

$loans = AssetLoan::whereNotNull('returned_at')->get();
foreach ($loans as $l) {
    echo "ID: {$l->id} | Status: {$l->status} | Returned At: {$l->returned_at}\n";
}
