<?php

namespace App\Http\Controllers;

use App\Models\Banner;

class BannerController extends Controller
{
    public function show($slug)
    {
        $banner = Banner::where('slug', $slug)->firstOrFail();
        $products = $banner->products()->latest()->get();

        return view('banner.show', compact('banner', 'products'));
    }
}
