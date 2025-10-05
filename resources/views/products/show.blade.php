@extends('layouts.app')

@section('content')
{{-- Panggil Livewire Component --}}
@livewire('product-detail', ['slug' => $product->slug])
@endsection