<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;

class ImageConverterService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Convert file upload apapun ke WebP, return isi binary-nya
     */
    public function encodeToWebp(UploadedFile $file, int $maxWidth = 1000, int $quality = 85): string
    {
        $image = $this->manager->read($file->getRealPath());

        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        return (string) $image->toWebp($quality);
    }
}
