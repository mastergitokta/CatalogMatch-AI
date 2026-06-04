<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductImageHashService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductImageHashService $imageHashService,
    ) {
    }

    public function index(): View
    {
        $products = Product::query()
            ->with('images')
            ->latest('created_at')
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['images']);

        DB::transaction(function () use ($request, $data): void {
            $product = Product::create([
                ...$data,
                'img_produk' => null,
                'hash_img' => null,
            ]);

            $this->storeUploadedImages($product, $request->file('images', []));
            $this->syncPrimaryImageColumns($product);
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load('images');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $deletedImageIds = collect($data['deleted_image_ids'] ?? []);
        $primaryImageId = $data['primary_image_id'] ?? null;

        unset($data['images'], $data['deleted_image_ids'], $data['primary_image_id']);

        DB::transaction(function () use ($request, $product, $data, $deletedImageIds, $primaryImageId): void {
            $product->update($data);

            if ($deletedImageIds->isNotEmpty()) {
                $imagesToDelete = $product->images()
                    ->whereIn('id', $deletedImageIds)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->deleteImage($image->image_path);
                    $image->delete();
                }
            }

            $this->storeUploadedImages($product, $request->file('images', []));
            $this->updatePrimaryImage($product, $primaryImageId);
            $this->syncPrimaryImageColumns($product);
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product): void {
            $product->load('images');

            foreach ($product->images as $image) {
                $this->deleteImage($image->image_path);
            }

            if ($product->images->isEmpty()) {
                $this->deleteImage($product->img_produk);
            }

            $product->delete();
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * @param  array<int, UploadedFile>  $images
     */
    private function storeUploadedImages(Product $product, array $images): void
    {
        if ($images === []) {
            return;
        }

        $nextSortOrder = (int) $product->images()->max('sort_order') + 1;
        $hasExistingImages = $product->images()->exists();

        foreach ($images as $index => $image) {
            $path = $image->store('products', 'public');

            $product->images()->create([
                'image_path' => $path,
                'hash_img' => $this->generateImageHash($image),
                'is_primary' => ! $hasExistingImages && $index === 0,
                'sort_order' => $nextSortOrder++,
            ]);
        }
    }

    private function generateImageHash(?UploadedFile $image): ?string
    {
        return $this->imageHashService->hashUploadedFile($image);
    }

    private function updatePrimaryImage(Product $product, mixed $primaryImageId): void
    {
        $images = $product->images()->get();

        if ($images->isEmpty()) {
            return;
        }

        $target = null;

        if (filled($primaryImageId)) {
            $target = $images->firstWhere('id', (int) $primaryImageId);
        }

        $target ??= $images->firstWhere('is_primary', true);
        $target ??= $images->sortBy('sort_order')->first();

        $product->images()->update(['is_primary' => false]);

        if ($target) {
            $target->forceFill(['is_primary' => true])->save();
        }
    }

    private function syncPrimaryImageColumns(Product $product): void
    {
        $primaryImage = $product->images()->get()->firstWhere('is_primary', true)
            ?? $product->images()->orderBy('sort_order')->first();

        if (! $primaryImage) {
            $product->forceFill([
                'img_produk' => null,
                'hash_img' => null,
            ])->saveQuietly();

            return;
        }

        $product->forceFill([
            'img_produk' => $primaryImage->image_path,
            'hash_img' => $primaryImage->hash_img,
        ])->saveQuietly();
    }

    private function deleteImage(?string $path): void
    {
        if (blank($path) || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
