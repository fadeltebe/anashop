<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;

class LiveProducts extends Component
{
    use WithPagination;

    public $perPage = 12;

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function render()
    {
        $products = Product::live()->paginate($this->perPage);

        return view('livewire.live-products', [
            'products' => $products,
        ]);
    }
}
