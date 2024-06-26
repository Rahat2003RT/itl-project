<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('dashboard', [UserController::class, 'dashboard'
    ])->name('dashboard');
});



Route::middleware('guest')->group(function(){
    Route::get('register', [UserController::class, 'create'])->name('register');
    Route::post('register', [UserController::class, 'store'])->name('user.store');
    
    Route::get('login', [UserController::class, 'login'])->name('login');
    Route::post('login', [UserController::class, 'loginAuth'])->name('login.auth');
});




Route::middleware('auth')->group(function(){
    Route::get('verify-email', function () {
        return view('user.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
    
        return redirect()->route('dashboard');
    })->middleware('signed')->name('verification.verify');

    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::get('profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
    
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:5,1')->name('verification.send');

    Route::get('logout', [UserController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::put('users/{id}/update', [UserController::class, 'update'])->name('users.update');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}/update', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}/destroy', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
    Route::post('brands', [BrandController::class, 'store'])->name('brands.store');
    Route::put('brands/{brand}/update', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('brands/{brand}/destroy', [BrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::put('products/{product}/update', [ProductController::class, 'update'])->name('products.update');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::delete('products/{product}/destroy', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('remove-product-image/{id}', [ProductController::class, 'removeProductImage'])->name('admin.removeProductImage');
    //Route::resource('/users', UserController::class);
    //Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    
});

Route::prefix('manager')->middleware(['auth', 'role:manager,admin'])->name('manager.')->group(function () {
    Route::get('dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');

    Route::get('products', [ManagerController::class, 'index'])->name('products.index');
    Route::post('products', [ManagerController::class, 'store'])->name('products.store');
    Route::put('products/{product}/update', [ManagerController::class, 'update'])->name('products.update');
    Route::delete('products/{product}/destroy', [ManagerController::class, 'destroy'])->name('products.destroy');
    Route::delete('remove-product-image/{id}', [ManagerController::class, 'removeProductImage'])->name('admin.removeProductImage');
    
});


//Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('catalog/{category_name}', [CatalogController::class, 'index'])->name('catalog.filter');
Route::get('catalog/product/{product_id}', [CatalogController::class, 'show'])->name('catalog.product.show');
//Route::post('catalog/product/{product_id}', [CatalogController::class, 'store'])->name('catalog.product.add');
Route::post('/catalog/product/{id}/review', [ProductController::class, 'storeReview'])->name('catalog.product.review');

Route::middleware('auth')->group(function() {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart',  [CartController::class, 'index'])->name('cart.index');
});
