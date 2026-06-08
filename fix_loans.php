<?php
\App\Models\AssetLoan::where('status', 'pending')
    ->whereNull('lender_id')
    ->get()
    ->each(function($l) {
        $l->update(['lender_id' => $l->asset->user_id]);
        echo "Fixed loan {$l->id}\n";
    });