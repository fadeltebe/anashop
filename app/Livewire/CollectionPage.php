<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductCollection;

class CollectionPage extends Component
{
    use WithPagination;

    public $collection;
    public $slug;
    public $perPage = 12;

    protected $paginationTheme = 'tailwind';

    public function mount($slug)
    {
        $this->slug = $slug;

        // Ambil koleksi berdasarkan slug
        $this->collection = ProductCollection::with('items.product')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function loadMore()
    {
        $this->perPage += 12;
        $this->resetPage(); // penting
    }

    public function render()
    {
        $products = Product::whereIn(
            'id',
            $this->collection->items->pluck('product_id') // ambil list product_id
        )

            ->paginate($this->perPage);

        return view('livewire.collection-page', [
            'collection' => $this->collection,
            'products' => $products,
        ]);
    }
}
