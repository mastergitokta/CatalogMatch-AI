<?php

use App\Models\ProductImage;
use App\Services\ProductImageHashService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('products:rehash-images', function (ProductImageHashService $imageHashService) {
    $updated = 0;
    $missing = 0;

    ProductImage::query()
        ->with('product')
        ->orderBy('id')
        ->chunkById(100, function ($images) use ($imageHashService, &$updated, &$missing): void {
            foreach ($images as $image) {
                $hash = $imageHashService->hashStoragePath($image->image_path);

                if (! $hash) {
                    $missing++;
                    $this->warn("Gagal hash image ID {$image->id}: {$image->image_path}");

                    continue;
                }

                $image->forceFill([
                    'hash_img' => $hash,
                ])->save();

                if ($image->is_primary && $image->product) {
                    $image->product->forceFill([
                        'img_produk' => $image->image_path,
                        'hash_img' => $hash,
                    ])->saveQuietly();
                }

                $updated++;
            }
        });

    $this->info("Re-hash selesai. Updated: {$updated}, missing: {$missing}");
})->purpose('Recalculate perceptual hashes for all stored product images');
