<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class ImageService
{

    public static function storeCompressed(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 1280,
        int $quality = 80
    ): string {
        $filename  = Str::uuid() . '.webp';
        $directory = storage_path("app/public/{$folder}");

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->decode($file->getRealPath());

        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        $image->encode(new WebpEncoder(quality: $quality))->save("{$directory}/{$filename}");

        return "{$folder}/{$filename}";
    }
}
