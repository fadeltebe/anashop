<?php

use App\Models\Category;
use App\Livewire\HomePage;
use App\Livewire\CheckoutPage;
use App\Livewire\LiveProducts;
use App\Livewire\ShoppingCart;
use App\Livewire\FlashSalePage;
use App\Livewire\ProductDetail;
use App\Livewire\CategoryProducts;
use Illuminate\Support\Facades\Route;
use App\Livewire\FeaturedProductsPage;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\WishlistController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', HomePage::class)->name('home');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/flash-sale', FlashSalePage::class)->name('flash-sale.all');
Route::get('/featured-products', FeaturedProductsPage::class)->name('featured.all');
Route::get('/live-products', LiveProducts::class)->name('live.all');


// Route::get('/product/{slug}', ProductDetail::class)->name('product.detail');
// Jika pakai Controller biasa
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/banner/{slug}', [BannerController::class, 'show'])->name('banner.show');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');


// Cart
Route::get('/cart', ShoppingCart::class)->name('cart.index');

// Wishlist (optional - untuk nanti)
Route::get('/wishlist', function () {
    return view('wishlist.index');
})->name('wishlist.index');

// Auth Routes (jika belum ada)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

Route::get('/checkout', CheckoutPage::class)->name('checkout');
