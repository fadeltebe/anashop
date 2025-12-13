<div>

    {{-- Hero Section --}}
    @include('components.hero')

    {{-- Categories --}}
    @if($categories->isNotEmpty())
    @include('components.categories', ['categories' => $categories])
    @endif

    {{-- Dynamic Product Collections --}}
    @foreach($collections as $collection)
    @include('components.collection-block', [
    'collection' => $collection
    ])
    @endforeach

    {{-- Recommended Product Scroll --}}
    @livewire('recommended-product-scroll')

</div>