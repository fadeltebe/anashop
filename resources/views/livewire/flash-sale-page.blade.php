@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h2 class="text-xl font-bold flex items-center gap-2 mb-4">
        <img src="/images/fire.gif" alt="flame" class="w-6 h-6">
        Flash Sale
    </h2>

    <x-product-grid :products="$products" />
</div>
@endsection