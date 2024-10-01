<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController; 
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('home', [HomeController::class,'index'])->middleware(['auth', 'verified'])->name('home');

Route::get('/product', function () {
    return view('admin.product');
})->middleware(['auth', 'verified'])->name('product');

Route::get('/brand', function () {
    return view('admin.brand');
})->middleware(['auth', 'verified'])->name('brand');

Route::get('/category', function () {
    return view('admin.category');
})->middleware(['auth', 'verified'])->name('category');

//Route::get('product', [ProductController::class,'index'])->middleware(['auth', 'verified'])->name('product');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/category', [CategoryController::class, 'index']); //n
// Halaman form create
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
// Proses form create
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

Route::get('/brand', [BrandController::class, 'index']);
Route::get('/product', [ProductController::class, 'index']);

// Route untuk menampilkan form kategori
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');

// Route untuk menyimpan kategori baru
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');


require __DIR__.'/auth.php';
