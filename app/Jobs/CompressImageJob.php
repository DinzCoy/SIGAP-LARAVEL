<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class CompressImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $tempPath,
        protected string $folder,
        protected string $modelClass,
        protected int $modelId,
        protected string $columnName,
        protected int $maxWidth = 1280,
        protected int $quality = 80
    ) {}

    public function handle(): void
    {
        $oldPath = storage_path("app/public/{$this->tempPath}");

        if (!file_exists($oldPath)) {
            return;
        }

        $filename  = Str::uuid() . '.webp';
        $directory = storage_path("app/public/{$this->folder}");

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $newPath     = "{$directory}/{$filename}";
        $newRelative = "{$this->folder}/{$filename}";

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($oldPath);

            if ($image->width() > $this->maxWidth) {
                $image->scaleDown(width: $this->maxWidth);
            }

            $image->encode(new WebpEncoder(quality: $this->quality))->save($newPath);

            $model = $this->modelClass::find($this->modelId);
            if ($model) {

                $model->update([
                    $this->columnName => $newRelative
                ]);
            }

            @unlink($oldPath);
        } catch (\Throwable $e) {

            if (file_exists($newPath)) {
                @unlink($newPath);
            }
            logger()->error("Gagal melakukan kompresi background untuk {$this->tempPath}: " . $e->getMessage());
            throw $e;
        }
    }
}
