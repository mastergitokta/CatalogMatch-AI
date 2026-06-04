@extends('layouts.app')

@section('title', 'Detail Product')

@section('content')
    <div class="detail-grid">
        <section class="detail-stage">
            <section class="panel detail-card">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->nama_produk }}">
                @else
                    <div class="card-image"></div>
                @endif
            </section>

            @if ($product->images->isNotEmpty())
                <section class="panel">
                    <p class="meta">Gallery</p>
                    <h2>Semua gambar produk</h2>
                    <div class="gallery-grid">
                        @foreach ($product->images as $image)
                            <div class="gallery-card">
                                <img src="{{ $image->image_url }}" alt="{{ $product->nama_produk }}">
                                <span class="badge {{ $image->is_primary ? '' : 'off' }}">
                                    {{ $image->is_primary ? 'Cover Utama' : 'Gallery' }}
                                </span>
                                <div class="gallery-meta">
                                    <div>
                                        <strong>Path</strong>
                                        <span>{{ $image->image_path }}</span>
                                    </div>
                                    <div>
                                        <strong>Hash</strong>
                                        <span>{{ $image->hash_img ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </section>

        <aside class="panel detail-info">
            <div>
                <p class="meta">Detail Product</p>
                <h1>{{ $product->nama_produk }}</h1>
            </div>

            <div>
                <span class="badge {{ $product->active ? '' : 'off' }}">
                    {{ $product->active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <p class="detail-copy">{{ $product->deskripsi_produk ?: 'Belum ada deskripsi produk.' }}</p>

            <div class="stats-grid">
                <div class="stat-card">
                    <strong>Image Utama</strong>
                    <span>{{ $product->img_produk ?: '-' }}</span>
                </div>
                <div class="stat-card">
                    <strong>Jumlah Gambar</strong>
                    <span>{{ $product->images->count() }}</span>
                </div>
                <div class="stat-card">
                    <strong>Hash Image Utama</strong>
                    <span>{{ $product->hash_img ?: '-' }}</span>
                </div>
                <div class="stat-card">
                    <strong>Created By</strong>
                    <span>{{ $product->created_by ?: '-' }}</span>
                </div>
                <div class="stat-card">
                    <strong>Update By</strong>
                    <span>{{ $product->update_by ?: '-' }}</span>
                </div>
                <div class="stat-card">
                    <strong>Created At</strong>
                    <span>{{ $product->created_at?->format('d M Y H:i') ?: '-' }}</span>
                </div>
                <div class="stat-card">
                    <strong>Update At</strong>
                    <span>{{ $product->update_at?->format('d M Y H:i') ?: '-' }}</span>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('products.edit', $product) }}" class="button">Edit</a>
                <a href="{{ route('products.index') }}" class="button secondary">Kembali</a>
            </div>
        </aside>
    </div>
@endsection
