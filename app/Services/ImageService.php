<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    public function replace(?string $current, ?UploadedFile $file, bool $remove, string $directory): ?string
    {
        if ($file) {
            $this->delete($current);

            return $this->store($file, $directory);
        }

        if ($remove) {
            $this->delete($current);

            return null;
        }

        return $current;
    }

    public static function url(?string $path): ?string
    {
        return $path ? '/storage/'.ltrim($path, '/') : null;
    }
}
