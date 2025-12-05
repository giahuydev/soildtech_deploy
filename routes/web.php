<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| CONTROLLERS USER
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Product\CartController;
use App\Http\Controllers\Product\ProductController as UserProductController;
use App\Http\Controllers\BrandController as UserBrandController;
use App\Http\Controllers\Payment\MoMoController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| CONTROLLERS ADMIN
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductVariantController;

/*
|--------------------------------------------------------------------------
| HOME PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

// Login – Register form
Route::get('/login', [LoginController::class, 'showLoginRegister'])->name('login');

// Submit login
Route::post('/login', [LoginController::class, 'login']);

// Submit register
Route::post('/register', [LoginController::class, 'register'])->name('register');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Google OAuth
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


/*
|--------------------------------------------------------------------------
| FORGOT PASSWORD (THIẾU → ĐÃ BỔ SUNG)
|--------------------------------------------------------------------------
*/

// Form nhập email để gửi link reset
// Hiển thị form nhập email để nhận link reset password
Route::get('/forgot_password', function () {
    return view('auth.forgot_password');
})
->middleware('guest')
->name('password.request');

// Gửi email reset password
Route::post('/forgot_password', [LoginController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

// Hiển thị form nhập mật khẩu mới
Route::get('/reset_password/{token}', function ($token) {
    return view('auth.reset_password', ['token' => $token]);
})
->middleware('guest')
->name('password.reset');

// Submit để đặt mật khẩu mới
Route::post('/reset_password', [LoginController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');


/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION ROUTES
|--------------------------------------------------------------------------
*/

// Trang yêu cầu xác thực email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Link xác thực email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login')->with('success', 'Email đã được xác thực!');
})->middleware(['signed'])->name('verification.verify');

// Gửi lại email verify
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Email xác thực đã được gửi lại.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Gửi lại từ trang login (không cần login)
Route::post('/email/resend', [LoginController::class, 'resendVerificationEmail'])
    ->middleware('throttle:6,1')
    ->name('verification.resend');


/*
|--------------------------------------------------------------------------
| USER PROFILE + ORDERS (AUTH + VERIFIED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Orders
    Route::get('/orders', [UserController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [UserController::class, 'orderDetail'])->name('order.detail');
});


/*
|--------------------------------------------------------------------------
| CART ROUTES (AUTH + VERIFIED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('cart')
    ->name('cart.')
    ->group(function () {

    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});


/*
|--------------------------------------------------------------------------
| PRODUCT ROUTES (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/san-pham', [UserProductController::class, 'index'])->name('shop.index');
Route::get('/san-pham/{slug}.html', [UserProductController::class, 'detail'])->name('shop.detail');
Route::get('/danh-muc/{slug}', [UserProductController::class, 'getByCategory'])->name('shop.category');
Route::get('/hot-sale', [UserProductController::class, 'hotSale'])->name('shop.hotSale');


/*
|--------------------------------------------------------------------------
| BRAND ROUTES (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/thuong-hieu', [UserBrandController::class, 'index'])->name('brands.index');
Route::get('/thuong-hieu/{slug}', [UserBrandController::class, 'show'])->name('brands.show');


/*
|--------------------------------------------------------------------------
| PAYMENT (AUTH + VERIFIED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('payment')
    ->name('payment.')
    ->group(function () {

    Route::get('/checkout', [MoMoController::class, 'showCheckout'])->name('checkout');
    Route::post('/process', [MoMoController::class, 'processPayment'])->name('process');

    Route::get('/success/{orderId}', [MoMoController::class, 'success'])->name('success');
    Route::get('/failed/{orderId}', [MoMoController::class, 'failed'])->name('failed');
});

// Callback MoMo
Route::get('/payment/momo/callback', [MoMoController::class, 'callback'])->name('momo.callback');
Route::post('/payment/momo/ipn', [MoMoController::class, 'ipn'])->name('momo.ipn');


/*
|--------------------------------------------------------------------------
| OTHER PAGES
|--------------------------------------------------------------------------
*/
Route::view('/return-policy', 'user.return_policy')->name('return.policy');
Route::view('/about', 'user.about')->name('about');
Route::view('/contact', 'user.contact')->name('contact');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', CheckAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');

    /*
    |--------------------------------------------------------------------------
    | CRUD PRODUCTS
    |--------------------------------------------------------------------------
    */
    Route::resource('products', AdminProductController::class);

    /*
    |--------------------------------------------------------------------------
    | CRUD CATEGORIES
    |--------------------------------------------------------------------------
    */
    Route::resource('categories', CategoryController::class);

    /*
    |--------------------------------------------------------------------------
    | CRUD BRANDS
    |--------------------------------------------------------------------------
    */
    Route::resource('brands', AdminBrandController::class);

    /*
    |--------------------------------------------------------------------------
    | PRODUCT VARIANTS
    |--------------------------------------------------------------------------
    */
    Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])
        ->name('product_variants.store');
    Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy'])
        ->name('product_variants.destroy');
    Route::get('variants', [ProductVariantController::class, 'index'])
        ->name('product_variants.index');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ORDERS
    |--------------------------------------------------------------------------
    */
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{orderId}', [DashboardController::class, 'orderDetail'])->name('orders.detail');
    Route::post('/orders/{orderId}/update-status', [DashboardController::class, 'updateOrderStatus'])
        ->name('orders.update_status');
});
