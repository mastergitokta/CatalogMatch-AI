@extends('layouts.app')

@section('title', 'Product')

@section('content')
    <section class="hero">
        <div>
            <p class="meta">Product Catalog</p>
            <h1>Kelola produk dalam satu tempat.</h1>
            <p>Tambah, ubah, lihat detail, dan hapus data produk dari dashboard sederhana ini.</p>
        </div>
        <a href="{{ route('products.create') }}" class="button">Buat Produk</a>
    </section>

    @if ($products->isEmpty())
        <div class="panel empty-state">
            Belum ada data produk.
        </div>
    @else
        <div class="grid product-grid">
            @foreach ($products as $product)
                <article class="card">
                    @if ($product->image_url)
                        <img class="card-image" src="{{ $product->image_url }}" alt="{{ $product->nama_produk }}">
                    @else
                        <div class="card-image"></div>
                    @endif

                    <div class="card-body">
                        <span class="badge {{ $product->active ? '' : 'off' }}">
                            {{ $product->active ? 'Aktif' : 'Nonaktif' }}
                        </span>

                        <h2>{{ $product->nama_produk }}</h2>
                        <p class="meta">
                            {{ \Illuminate\Support\Str::limit($product->deskripsi_produk, 120) ?: 'Belum ada deskripsi produk.' }}
                        </p>
                        <p class="meta">Jumlah gambar: {{ $product->images->count() }}</p>
                        <p class="meta">Hash utama: {{ $product->hash_img ?: '-' }}</p>

                        <div class="actions">
                            <a href="{{ route('products.show', $product) }}" class="button secondary">Detail</a>
                            <a href="{{ route('products.edit', $product) }}" class="button secondary">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            {{ $products->links() }}
        </div>
    @endif
@endsection
