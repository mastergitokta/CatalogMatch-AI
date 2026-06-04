@extends('layouts.app')

@section('title', 'Tambah Product')

@section('content')
    <div class="panel">
        <p class="meta">Form Create</p>
        <h1>Tambah produk baru</h1>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @include('products._form', ['submitLabel' => 'Simpan Produk'])
        </form>
    </div>
@endsection
