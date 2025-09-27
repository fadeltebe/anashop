@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h2 class="text-xl font-bold flex items-center gap-2 mb-4">
        <img src="/images/fire.gif" alt="flame" class="w-6 h-6">
        Live
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($products as $product)
        <a href="{{ $product->slug }}" class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
            <div class="aspect-square">
                <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : asset('images/default-product.png') }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold line-clamp-2">{{ $product->name }}</h3>
                @if(!empty($product->rating))
                <div class="flex items-center mb-2 text-xs">
                    <div class="flex">
                        <x-rating :value="$product->rating" />
                    </div>
                </div>
                @endif
                <div class="text-orange-600 font-bold">
                    Rp{{ number_format($product->discount_price, 0, ',', '.') }}
                </div>
                <div class="text-gray-400 line-through text-sm">
                    Rp{{ number_format($product->price, 0, ',', '.') }}
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-6 flex justify-center">
        @if($products->hasMorePages())
        <button wire:click="loadMore" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
            Muat Lebih Banyak
        </button>
        @else
        <span class="text-gray-500">Semua produk sudah tampil.</span>
        @endif
    </div>
</div>
@endsection