<?php

use App\Livewire\Web\Auth\Login;
use App\Livewire\Web\Auth\Register;
use App\Livewire\Web\Auth\Verify;
use App\Livewire\Web\CartPage;
use App\Livewire\Web\Checkout;
use App\Livewire\Web\Home;
use App\Livewire\Web\OrderShow;
use App\Livewire\Web\Orders;
use App\Livewire\Web\Products;
use App\Livewire\Web\ServiceOrder;
use App\Livewire\Web\ProductShow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ===== المتجر =====
Route::get('/', Home::class)->name('web.home');
Route::get('/products', Products::class)->name('web.products');
Route::get('/products/{product}', ProductShow::class)->name('web.product');
Route::get('/services/{service}', ServiceOrder::class)->name('web.service');
Route::get('/cart', CartPage::class)->name('web.cart');
Route::get('/checkout', Checkout::class)->name('web.checkout');

// ===== الحساب =====
Route::get('/login', Login::class)->name('web.login');
Route::get('/register', Register::class)->name('web.register');
Route::get('/verify', Verify::class)->name('web.verify');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('web.home');
})->name('web.logout');

Route::get('/orders', Orders::class)->name('web.orders');
Route::get('/orders/{order}', OrderShow::class)->name('web.order');

// ===== صفحات ثابتة =====
Route::view('/about', 'pages.about')->name('web.about');
Route::view('/contact', 'pages.contact')->name('web.contact');
Route::view('/privacy', 'pages.privacy')->name('web.privacy');
Route::view('/returns', 'pages.returns')->name('web.returns');
