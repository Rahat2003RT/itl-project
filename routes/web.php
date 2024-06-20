<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
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
    
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
    
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:2,1')->name('verification.send');

    Route::get('logout', [UserController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('users', [AdminController::class, 'getAllUsers'])->name('admin.users');
    Route::post('users/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('admin.updateUserRole');

    Route::get('categories', [AdminController::class, 'getAllCategories'])->name('admin.categories');
    Route::post('categories', [AdminController::class, 'addCategory'])->name('admin.addCategories');
    Route::put('{category}', [AdminController::class, 'updateCategory'])->name('admin.updateCategory');
    Route::delete('{category}', [AdminController::class, 'destroyCategory'])->name('admin.destroyCategory');

    Route::get('brands', [AdminController::class, 'getAllBrands'])->name('admin.brands');
    Route::post('brands', [AdminController::class, 'addBrand'])->name('admin.addBrands');
    Route::put('brands/{brand}', [AdminController::class, 'updateBrand'])->name('admin.updateBrand');
    Route::delete('brands/{brand}', [AdminController::class, 'destroyBrand'])->name('admin.destroyBrand');

    Route::get('products', [AdminController::class, 'getAllProductsWithAllCategories'])->name('admin.products');
    Route::post('products', [AdminController::class, 'addProduct'])->name('admin.addProducts');
    Route::put('products/{product}', [AdminController::class, 'updateProduct'])->name('admin.updateProduct');
    Route::delete('products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.destroyProduct');
    Route::delete('remove-product-image/{id}', [AdminController::class, 'removeProductImage'])->name('admin.removeProductImage');
    //Route::resource('/users', UserController::class);
    //Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    
});


Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/filter', [CatalogController::class, 'filter'])->name('catalog.filter');