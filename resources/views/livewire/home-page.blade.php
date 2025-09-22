@extends('layouts.app')

@section('content')
@include('components.header')
@include('components.hero')

{{-- Kategori --}}
@include('components.categories', ['categories' => $categories])

{{-- Flash Sale --}}
@include('components.flash-sale', ['products' => $flashSales])

{{-- Produk Unggulan --}}
@include('components.featured-products', ['products' => $featuredProducts])

{{-- Produk Rekomendasi (infinite scroll) --}}
@include('components.products', ['products' => $recommendedProducts])
@endsection