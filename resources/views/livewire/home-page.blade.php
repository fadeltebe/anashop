@extends('layouts.app')

@section('content')

@include('components.hero')

{{-- Kategori --}}
@if($categories->isNotEmpty())
@include('components.categories', ['categories' => $categories])
@endif

{{-- Produk Live --}}
@if($liveProducts->isNotEmpty())
@include('components.live-products', ['products' => $liveProducts])
@endif

{{-- Flash Sale --}}
@if($flashSales->isNotEmpty())
@include('components.flash-sale', ['products' => $flashSales])
@endif

{{-- Produk Unggulan --}}
@if($featuredProducts->isNotEmpty())
@include('components.featured-products', ['products' => $featuredProducts])
@endif

{{-- Produk Rekomendasi (infinite scroll) --}}
@livewire('recommended-product-scroll')

@endsection