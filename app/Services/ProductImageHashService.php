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

        return Hash::fromBits($this->hexToBits($firstHash))
            ->distance(Hash::fromBits($this->hexToBits($secondHash)));
    }

    private function hexToBits(string $hex): string
    {
        $hex = strtolower(trim($hex));

        return collect(str_split($hex))
            ->map(fn (string $character): string => str_pad(base_convert($character, 16, 2), 4, '0', STR_PAD_LEFT))
            ->implode('');
    }
}
