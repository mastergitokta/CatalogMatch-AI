<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'product';

    public const UPDATED_AT = 'update_at';

    protected $fillable = [
        'nama_produk',
        'deskripsi_produk',
        'img_produk',
        'hash_img',
        'active',
        'created_by',
        'update_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getPrimaryImageAttribute(): ?ProductImage
    {
        $images = $this->relationLoaded('images')
            ? $this->images
            : $this->images()->get();

        return $images->firstWhere('is_primary', true) ?? $images->first();
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->primary_image?->image_url) {
            return $this->primary_image->image_url;
        }

        if (blank($this->img_produk)) {
            return null;
        }

        if (filter_var($this->img_produk, FILTER_VALIDATE_URL)) {
            return $this->img_produk;
        }

        return route('media.show', ['path' => ltrim($this->img_produk, '/')]);
    }
}
