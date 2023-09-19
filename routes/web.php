<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CuratechProductController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\VendorController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard', ['curatech_devices' => App\Models\CuratechProduct::all()]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Curatech Products
Route::get('/curatech_products', [CuratechProductController::class, 'index'])->middleware(['auth', 'verified'])->name('curatech_products');
Route::get('/curatech_product/{id}', [CuratechProductController::class, 'details'])->middleware(['auth', 'verified'])->name('curatech_product_details');
Route::get('/curatech_product/{id}/edit', [CuratechProductController::class, 'updatePage'])->middleware(['auth', 'verified'])->name('curatech_product_update');
Route::post('/curatech_product/{id}/edit', [CuratechProductController::class, 'update'])->middleware(['auth', 'verified'])->name('curatech_product_store');
Route::post('/curatech_product/{id}/add_component', [CuratechProductController::class, 'addComponent'])->middleware(['auth', 'verified'])->name('curatech_product_add_component');
Route::post('/curatech_product/{id}/remove_component', [CuratechProductController::class, 'removeComponent'])->middleware(['auth', 'verified'])->name('curatech_product_remove_component');
Route::get('/curatech_products/create', [CuratechProductController::class, 'create'])->middleware(['auth', 'verified'])->name('curatech_products.create');
Route::post('/curatech_products/create', [CuratechProductController::class, 'createProduct'])->middleware(['auth', 'verified'])->name('curatech_products.create_product');

// Purchases
Route::get('/purchases', [PurchasesController::class, 'get'])->middleware(['auth', 'verified'])->name('purchases');
Route::post('/purchases', [PurchasesController::class, 'updateStock'])->middleware(['auth', 'verified'])->name('purchases_update_stock');

// Components
Route::get('components', [ComponentController::class, 'get'])->middleware(['auth', 'verified'])->name('components');
Route::post('components/details/{id}/add_vendor', [ComponentController::class, 'addVendor'])->middleware(['auth', 'verified'])->name('components.vendor.add');
Route::get('components/details/{id}', [ComponentController::class, 'details'])->middleware(['auth', 'verified'])->name('components_details');
Route::post('components/details/{id}', [ComponentController::class, 'update'])->middleware(['auth', 'verified'])->name('components_update');
Route::post('/components/upload', [FileUploadController::class, 'uploadComponentsCSV'])->name('components_upload');
Route::get('/components/create', [ComponentController::class, 'createPage'])->name('components_create');
Route::post('/components/create', [ComponentController::class, 'create'])->name('components_create');

// Vendors
Route::get('vendors', [VendorController::class, 'index'])->middleware(['auth', 'verified'])->name('vendors');
Route::get('vendors/create', [VendorController::class, 'createPage'])->middleware(['auth', 'verified'])->name('vendors.create');
Route::post('vendors/create', [VendorController::class, 'create'])->middleware(['auth', 'verified'])->name('vendors.createVendor');
Route::get('vendors/{id}', [VendorController::class, 'details'])->middleware(['auth', 'verified'])->name('vendors.details');
Route::get('vendors/{id}/edit', [VendorController::class, 'edit'])->middleware(['auth', 'verified'])->name('vendors.edit');
Route::post('vendors/{id}/update', [VendorController::class, 'update'])->middleware(['auth', 'verified'])->name('vendors.update');
Route::delete('vendors/{id}', [VendorController::class, 'delete'])->middleware(['auth', 'verified'])->name('vendors.delete');

// Authentication
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
