@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="panel">
        <p class="meta">Form Edit</p>
        <h1>Edit {{ $product->nama_produk }}</h1>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('products._form', ['submitLabel' => 'Update Produk'])
        </form>
    </div>
@endsection
