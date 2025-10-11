<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 🔹 Menampilkan semua kategori
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    // 🔹 Menampilkan produk berdasarkan kategori (slug)
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = $category->products()->get(); // pastikan relasi sudah dibuat

        return view('categories.show', compact('category', 'products'));
    }
}
