<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Http\UploadedFile;
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

    /**
     * Convert file upload apapun ke WebP, return isi binary-nya
     */
    public function encodeToWebp(UploadedFile $file, int $maxWidth = 1000, int $quality = 85): string
    {
        $path = $file->getRealPath();

        \Log::info('Image upload debug', [
            'path' => $path,
            'exists' => $path ? file_exists($path) : false,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]);

        $contents = $path ? file_get_contents($path) : null;

        if (!$contents) {
            throw new Exception('Gagal membaca file upload — file kosong atau tidak ditemukan di path: ' . $path);
        }

        $image = $this->manager->read($contents);

        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        return (string) $image->toWebp($quality);
    }
}
