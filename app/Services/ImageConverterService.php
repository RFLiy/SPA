<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        // TemporaryUploadedFile dari Livewire: getRealPath() return path relatif
        // ke disk 'local', jadi kita ambil isi filenya lewat Storage facade
        $relativePath = $file->getRealPath();

        Log::info('Image upload debug', [
            'relativePath' => $relativePath,
            'existsOnLocalDisk' => Storage::disk('local')->exists($relativePath),
        ]);

        if (Storage::disk('local')->exists($relativePath)) {
            $contents = Storage::disk('local')->get($relativePath);
        } else {
            // fallback: mungkin memang path absolut biasa (upload non-Livewire)
            $contents = file_exists($relativePath) ? file_get_contents($relativePath) : null;
        }

        if (!$contents) {
            throw new Exception('Gagal membaca file upload dari path: ' . $relativePath);
        }

        $image = $this->manager->read($contents);

        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        return (string) $image->toWebp($quality);
    }
}
