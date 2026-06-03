<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssetLoan;
use App\Services\Notification\Assets\AssetLoanReminderNotification;
use Carbon\Carbon;

class SendLoanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat notifikasi untuk peminjaman aset yang H-1 atau Overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Cari semua peminjaman aktif yang tipe-nya pinjam
        $activeLoans = AssetLoan::where('status', 'active')
            ->where('type', 'pinjam')
            ->whereNotNull('due_date')
            ->get();

        $countReminder = 0;
        $countOverdue = 0;

        foreach ($activeLoans as $loan) {
            $dueDate = Carbon::parse($loan->due_date)->startOfDay();

            // Kondisi 1: H-1 (Tenggat waktu adalah besok)
            if ($dueDate->equalTo($tomorrow)) {
                AssetLoanReminderNotification::kirim($loan, false);
                $countReminder++;
            }
            // Kondisi 2: Overdue (Tenggat waktu kurang dari hari ini)
            elseif ($dueDate->lessThan($today)) {
                AssetLoanReminderNotification::kirim($loan, true);
                $countOverdue++;
            }
        }

        $this->info("Berhasil mengirim $countReminder pengingat H-1 dan $countOverdue pengingat Overdue.");
    }
}
