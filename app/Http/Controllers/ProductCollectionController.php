<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCollection;

class ProductCollectionController extends Controller
{
    public function index()
    {
        $collections = ProductCollection::with([
            'items.product' => fn($q) => $q->where('is_active', true)
        ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('home', compact('collections'));
    }

    public function show($slug)
    {
        $collection = ProductCollection::where('slug', $slug)->firstOrFail();

        // Ambil semua produk dari item
        $products = $collection->items()
            ->whereHas('product')
            ->with('product')
            ->get()
            ->pluck('product'); // ambil hanya produk, bukan item

        return view('collection.show', compact('collection', 'products'));
    }
}
