<x-layouts.app>
    {{-- Panggil Livewire Component --}}
    @livewire('product-detail', ['slug' => $product->slug])
</x-layouts.app>