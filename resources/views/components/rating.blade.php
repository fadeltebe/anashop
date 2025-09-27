@props(['value' => 0]) {{-- default rating 0 --}}

<div class="flex items-center text-xs">
    <div class="flex">
        {{-- Bintang penuh --}}
        @for($i = 0; $i < floor($value); $i++) ⭐ @endfor {{-- Setengah bintang --}} @if($value - floor($value)>= 0.5)
            🌟
            @php $stars = floor($value) + 1; @endphp
            @else
            @php $stars = floor($value); @endphp
            @endif

            {{-- Bintang kosong --}}
            @for($i = $stars; $i < 5; $i++) ✰ @endfor </div>

                {{-- Angka rating --}}
                <span class="text-gray-500 ml-1">({{ number_format($value, 1) }})</span>
    </div>