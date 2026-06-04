<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Jenssegers\ImageHash\Hash;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class ProductImageHashService
{
    private ImageHash $hasher;

    public function __construct()
    {
        $this->hasher = new ImageHash(new DifferenceHash());
    }

    public function hashUploadedFile(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        return $this->hashAbsolutePath($file->getRealPath());
    }

    public function hashStoragePath(?string $path, string $disk = 'public'): ?string
    {
        if (blank($path) || filter_var($path, FILTER_VALIDATE_URL) || ! Storage::disk($disk)->exists($path)) {
            return null;
        }

        return $this->hashAbsolutePath(Storage::disk($disk)->path($path));
    }

    public function hashAbsolutePath(?string $path): ?string
    {
        if (blank($path) || ! is_file($path)) {
            return null;
        }

        return $this->hasher->hash($path)->toHex();
    }

    public function distance(?string $firstHash, ?string $secondHash): ?int
    {
        if (blank($firstHash) || blank($secondHash)) {
            return null;
        }

        return Hash::fromHex(strtolower($firstHash))
            ->distance(Hash::fromHex(strtolower($secondHash)));
    }
}
