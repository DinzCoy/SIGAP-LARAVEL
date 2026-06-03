<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;

class CompressExistingImages extends Command
{

    protected $signature = 'images:compress
                            {--dry-run : Preview saja, tidak ada file yang diubah}
                            {--folder= : Proses hanya folder tertentu}';

    protected $description = 'Kompres semua foto lama yang tersimpan mentah ke format WebP';

    private array $folderConfig = [
        'ticket-photos'  => ['maxWidth' => 1280, 'quality' => 80],
        'device_images'  => ['maxWidth' => 800,  'quality' => 80],
        'profile-photos' => ['maxWidth' => 400,  'quality' => 85],
    ];

    private array $dbColumns = [
        'tickets'      => ['table' => 'tickets',      'column' => 'photo_path'],
        'users'        => ['table' => 'users',         'column' => 'photo_path'],
        'device_names' => ['table' => 'device_names',  'column' => 'image'],
    ];

    public function handle(): int
    {
        $isDryRun     = $this->option('dry-run');
        $targetFolder = $this->option('folder');

        $folders = $targetFolder
            ? [$targetFolder => $this->folderConfig[$targetFolder] ?? ['maxWidth' => 1280, 'quality' => 80]]
            : $this->folderConfig;

        if ($isDryRun) {
            $this->warn('🔍 MODE DRY-RUN: Tidak ada file yang akan diubah.');
        }

        $totalFiles     = 0;
        $totalProcessed = 0;
        $totalSkipped   = 0;
        $totalFailed    = 0;
        $savedBytes     = 0;

        foreach ($folders as $folder => $config) {
            $directory = storage_path("app/public/{$folder}");

            if (!is_dir($directory)) {
                $this->line("📂 Folder <comment>{$folder}</comment> tidak ditemukan, dilewati.");
                continue;
            }

            $files = collect(scandir($directory))
                ->filter(fn($f) => !in_array($f, ['.', '..']))
                ->filter(fn($f) => preg_match('/\.(jpg|jpeg|png|gif)$/i', $f))
                ->values();

            if ($files->isEmpty()) {
                $this->line("✅ Folder <info>{$folder}</info>: tidak ada foto lama (semua sudah WebP).");
                continue;
            }

            $this->info("\n📁 Folder: <comment>{$folder}</comment> ({$files->count()} foto lama ditemukan)");
            $this->line(str_repeat('─', 60));

            foreach ($files as $filename) {
                $totalFiles++;
                $oldPath  = "{$directory}/{$filename}";
                $oldSize  = filesize($oldPath);
                $oldRelative = "{$folder}/{$filename}";

                $newFilename = Str::uuid() . '.webp';
                $newPath     = "{$directory}/{$newFilename}";
                $newRelative = "{$folder}/{$newFilename}";

                if ($isDryRun) {
                    $this->line("  🔎 <comment>{$filename}</comment> (~" . round($oldSize / 1024) . " KB) → akan dikompres");
                    $totalProcessed++;
                    continue;
                }

                try {
                    $manager = new ImageManager(new Driver());
                    $image = $manager->decode($oldPath);

                    if ($image->width() > $config['maxWidth']) {
                        $image->scaleDown(width: $config['maxWidth']);
                    }

                    $image->encode(new WebpEncoder(quality: $config['quality']))->save($newPath);

                    $newSize  = filesize($newPath);
                    $saved    = $oldSize - $newSize;
                    $savedPct = $oldSize > 0 ? round(($saved / $oldSize) * 100) : 0;
                    $savedBytes += $saved;

                    $this->updateDatabase($oldRelative, $newRelative);

                    @unlink($oldPath);

                    $this->line(
                        "  ✅ <info>{$filename}</info> → <comment>{$newFilename}</comment> " .
                        "(<fg=green>-{$savedPct}%</>) " .
                        round($oldSize / 1024) . "KB → " . round($newSize / 1024) . "KB"
                    );
                    $totalProcessed++;
                } catch (\Throwable $e) {
                    $this->error("  ❌ Gagal: {$filename} — {$e->getMessage()}");
                    $totalFailed++;
                }
            }
        }

        $this->line("\n" . str_repeat('═', 60));
        $this->info('📊 RINGKASAN KOMPRESI');
        $this->line(str_repeat('─', 60));
        $this->line("  Total foto ditemukan : <comment>{$totalFiles}</comment>");
        $this->line("  Berhasil dikompres   : <info>{$totalProcessed}</info>");
        $this->line("  Gagal                : <fg=red>{$totalFailed}</>");

        if (!$isDryRun && $savedBytes > 0) {
            $this->line("  Total ruang dihemat  : <fg=green>" . round($savedBytes / 1024 / 1024, 2) . " MB</>");
        }

        if ($isDryRun) {
            $this->warn("\n💡 Jalankan tanpa --dry-run untuk mulai mengompres.");
        } else {
            $this->info("\n🎉 Kompresi selesai! Website kamu sekarang jauh lebih ringan.");
        }

        return self::SUCCESS;
    }

    private function updateDatabase(string $oldPath, string $newPath): void
    {
        foreach ($this->dbColumns as $dbConfig) {
            DB::table($dbConfig['table'])
                ->where($dbConfig['column'], $oldPath)
                ->update([$dbConfig['column'] => $newPath]);
        }
    }
}
