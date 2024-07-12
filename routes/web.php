<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\AdminCollectionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PickupPointController;
use App\Http\Controllers\Api\ApiProductController;
use App\Http\Controllers\Manager\ManagerCollectionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () { return view('welcome'); })->name('home');

Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    Route::get('/cart',  [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/products/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('products.toggleFavorite');
    Route::post('/product/{id}/favorite', [ProductController::class, 'addToFavorites'])->name('product.favorite');
    Route::delete('/product/{id}/favorite', [ProductController::class, 'removeFromFavorites'])->name('product.favorite.remove');

});


Route::middleware('guest')->group(function(){
    Route::get('register', [UserController::class, 'create'])->name('register');
    Route::post('register', [UserController::class, 'store'])->name('user.store');
    
    Route::get('login', [UserController::class, 'login'])->name('login');
    Route::post('login', [UserController::class, 'loginAuth'])->name('login.auth');
});


Route::middleware('auth')->group(function(){
    Route::get('verify-email', function () { return view('user.verify-email'); })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard');
    })->middleware('signed')->name('verification.verify');
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
    
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:5,1')->name('verification.send');

    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::get('profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/cards', [UserController::class, 'storeCard'])->name('profile.cards.store');
    Route::delete('/profile/cards/{id}', [UserController::class, 'destroyCard'])->name('profile.cards.destroy');

    Route::get('addresses/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::post('addresses/update', [AddressController::class, 'update'])->name('addresses.update');

    Route::get('logout', [UserController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::put('users/{id}/update', [UserController::class, 'update'])->name('users.update');

    Route::resource('categories', CategoryController::class);

    Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
    Route::post('brands', [BrandController::class, 'store'])->name('brands.store');
    Route::put('brands/{brand}/update', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('brands/{brand}/destroy', [BrandController::class, 'destroy'])->name('brands.destroy');

    Route::resource('pickup-points', PickupPointController::class);

    Route::get('products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}/update', [AdminProductController::class, 'update'])->name('products.update');
    Route::get('products/{product}/manage', [AdminProductController::class, 'manage'])->name('products.manage');
    Route::post('products/{product}/manageUpdate', [AdminProductController::class, 'manageUpdate'])->name('products.manageUpdate');
    Route::delete('products/{product}/destroy', [AdminProductController::class, 'destroy'])->name('products.destroy');


    // Маршруты для атрибутов
    Route::get('/attributes', [AttributeController::class, 'index'])->name('attributes.index');
    Route::get('/attributes/create', [AttributeController::class, 'create'])->name('attributes.create');
    Route::post('/attributes', [AttributeController::class, 'store'])->name('attributes.store');
    Route::get('/attributes/{attribute}/edit', [AttributeController::class, 'edit'])->name('attributes.edit');
    Route::put('/attributes/{attribute}/update', [AttributeController::class, 'update'])->name('attributes.update');
    Route::delete('attributes/{attribute}/destroy', [AttributeController::class, 'destroy'])->name('attributes.destroy');

    // Маршруты для значений атрибутов
    Route::get('attributes/{attribute}/values', [AttributeValueController::class, 'index'])->name('attribute_values.index');
    Route::get('attributes/{attribute}/values/create', [AttributeValueController::class, 'create'])->name('attribute_values.create');
    Route::post('attributes/{attribute}/values', [AttributeValueController::class, 'store'])->name('attribute_values.store');
    Route::put('attribute_values/{attribute}/update', [AttributeValueController::class, 'update'])->name('attribute_values.update');
    Route::delete('attribute_values/{attribute_value}/destroy', [AttributeValueController::class, 'destroy'])->name('attribute_values.destroy');

    Route::resource('collections', AdminCollectionController::class);
    Route::get('collections/{collection}/manage', [AdminCollectionController::class, 'manage'])->name('collections.manage');
    Route::put('collections/{collection}/manage', [AdminCollectionController::class, 'manageUpdate'])->name('collections.manageUpdate');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/api/products', [ApiProductController::class, 'index']);
    Route::get('/api/categories/{category}/products', [ApiProductController::class, 'getByCategory']);

});

Route::prefix('manager')->middleware(['auth', 'role:manager,admin'])->name('manager.')->group(function () {
    Route::get('dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');

    Route::get('products', [ManagerController::class, 'index'])->name('products.index');
    Route::post('products', [ManagerController::class, 'store'])->name('products.store');
    Route::put('products/{product}/update', [ManagerController::class, 'update'])->name('products.update');
    Route::delete('products/{product}/destroy', [ManagerController::class, 'destroy'])->name('products.destroy');
    Route::delete('remove-product-image/{id}', [ManagerController::class, 'removeProductImage'])->name('admin.removeProductImage');

    Route::resource('collections', ManagerCollectionController::class);
    Route::get('collections/{collection}/manage', [ManagerCollectionController::class, 'manage'])->name('collections.manage');
    Route::put('collections/{collection}/manage', [ManagerCollectionController::class, 'manageUpdate'])->name('collections.manageUpdate');
    
});//Пока не приступал


Route::get('catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/search', [CatalogController::class, 'search'])->name('catalog.search');
Route::get('catalog/{category_name}', [CatalogController::class, 'index'])->name('catalog.filter');
Route::get('catalog/product/{product_id}', [CatalogController::class, 'show'])->name('catalog.product.show');
Route::post('/catalog/product/{id}/review', [ProductController::class, 'storeReview'])->name('catalog.product.review');

Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');



