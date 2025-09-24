<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\FlashSalePage;
use App\Livewire\ProductDetail;
use App\Livewire\FeaturedProductsPage;
use App\Http\Controllers\BannerController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', HomePage::class)->name('home');
// routes/web.php
Route::get('/flash-sale', FlashSalePage::class)->name('flash-sale.all');
Route::get('/featured-products', FeaturedProductsPage::class)->name('featured.all');

Route::get('/product/{slug}', ProductDetail::class)->name('product.detail');

Route::get('/banner/{slug}', [BannerController::class, 'show'])->name('banner.show');
