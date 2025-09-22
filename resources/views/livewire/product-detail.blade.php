@extends('layouts.app')

@section('content')
<section class="py-8 bg-gray-100">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden grid md:grid-cols-2 gap-6">

            {{-- Gambar Produk --}}
            <div class="p-6">
                <!-- Foto Utama -->
                <div class="flex items-center justify-center mb-4">
                    <img id="mainImage" src="{{ $product['thumbnail'] }}" alt="{{ $product['title'] }}" class="rounded-lg object-cover max-h-96">
                </div>

                <!-- Foto Thumbnail Scroll -->
                @if(!empty($product['images']))
                <div class="flex space-x-3 overflow-x-auto scrollbar-hide">
                    @foreach($product['images'] as $img)
                    <img src="{{ $img }}" onclick="changeImage('{{ $img }}')" class="w-20 h-20 object-cover rounded cursor-pointer border hover:border-orange-500 transition">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Detail Produk --}}
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $product['title'] }}</h1>
                <p class="text-gray-600 mb-4">{{ $product['description'] }}</p>

                {{-- Rating --}}
                <div class="flex items-center mb-3">
                    @php
                    $fullStars = floor($product['rating']);
                    $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                    @endphp
                    <div class="flex text-yellow-400">
                        @for($i = 0; $i < $fullStars; $i++) ⭐ @endfor @if($halfStar) 🌟 @endif @for($i=$fullStars + ($halfStar ? 1 : 0); $i < 5; $i++) ☆ @endfor </div>
                            <span class="ml-2 text-gray-500">({{ number_format($product['rating'], 1) }})</span>
                    </div>

                    {{-- Harga --}}
                    <div class="mb-4">
                        <span class="text-2xl font-bold text-orange-600">
                            Rp{{ number_format($product['price'] * 15000, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Stok --}}
                    <p class="text-gray-500 text-sm mb-2">Stok tersedia: {{ $product['stock'] }}</p>
                    <p class="text-gray-500 text-sm mb-6">Kategori: {{ $product['category'] }}</p>

                    {{-- Tombol Aksi --}}
                    <div class="flex space-x-3">
                        <button class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">Tambah ke Keranjang</button>
                        <a href="{{ route('ecommerce.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
</section>

{{-- Script untuk ganti gambar --}}
@push('scripts')
<script>
    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }
</script>
@endpush
@endsection