@extends('layouts.app')

@section('content')

<section class="py-4 bg-gray-100">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden grid md:grid-cols-2 gap-6">

            {{-- Product Images --}}
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <img id="mainImage" src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->title }}" class="rounded-lg object-contain w-full max-h-[400px]">
                </div>

                {{-- Thumbnail Photos --}}
                @if(!empty($product->photos))
                <div class="flex space-x-3 overflow-x-auto scrollbar-hide">
                    @foreach($product->photos as $img)
                    @php
                    $url = asset('storage/' . $img);
                    @endphp
                    <img src="{{ $url }}" onclick="changeImage(this, '{{ $url }}')" class="thumbnail w-20 h-20 object-cover rounded cursor-pointer border border-gray-300 hover:border-orange-500 transition">
                    @endforeach
                </div>
                @endif

            </div>

            {{-- Product Details --}}
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $product->name }}</h1>
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

                    {{-- Price --}}
                    <div class="mb-4">
                        <span class="text-2xl font-bold text-orange-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Stock & Category --}}
                    <p class="text-gray-500 text-sm mb-2">Stok tersedia: {{ $product->stock }}</p>
                    <p class="text-gray-500 text-sm mb-6">Kategori: {{ $product->category->name }}</p>

                    {{-- Action Buttons --}}
                    <div class="flex space-x-3">
                        <button class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">Tambah ke Keranjang</button>
                        <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
</section>

@push('scripts')
<script>
    function changeImage(el, src) {
        // ganti gambar utama
        document.getElementById('mainImage').src = src;

        // reset semua thumbnail ke border default
        document.querySelectorAll('.thumbnail').forEach(img => {
            img.classList.remove('border-orange-500');
            img.classList.add('border-gray-300');
        });

        // tambahkan border aktif ke thumbnail yang diklik
        el.classList.remove('border-gray-300');
        el.classList.add('border-orange-500');
    }
</script>
@endpush


@endsection