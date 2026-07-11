<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Exception;

class ImageConverterService
{
    protected ImageManager $manager;

    public function __construct()
    {
        if (extension_loaded('gd')) {
            $this->manager = new ImageManager(new GdDriver());
        } elseif (extension_loaded('imagick')) {
            $this->manager = new ImageManager(new ImagickDriver());
        } else {
            throw new Exception('Ekstensi GD atau Imagick tidak tersedia di server.');
        }
    }

    public function encodeToWebp(UploadedFile $file, int $maxWidth = 1000, int $quality = 85): string
    {
        if (method_exists($file, 'readStream')) {
            $stream = $file->readStream();
            $contents = stream_get_contents($stream);
            fclose($stream);
        } else {
            $contents = file_get_contents($file->getRealPath());
        }

        Log::info('Image upload debug', [
            'class' => get_class($file),
            'contentLength' => $contents ? strlen($contents) : 0,
        ]);

        if (!$contents) {
            throw new Exception('Gagal membaca isi file upload.');
        }

        $image = $this->manager->read($contents);

        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        return (string) $image->toWebp($quality);
    }
}
