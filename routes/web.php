<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController; 
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Filament\Pages\OutTransactionCart;
use App\Http\Controllers\OutTransactionController;
use App\Http\Controllers\OutTransactionCartController;
use App\Http\Controllers\InTransactionController;
use App\Models\OutTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


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

// routes/web.php
Route::get('/outtransactions/cart', [OutTransactionController::class, 'showCart'])->name('outtransactions.cart');
Route::post('/outtransactions/cart/add', [OutTransactionController::class, 'addToCart'])->name('outtransactions.add_to_cart');

Route::get('/outtransaction/cart', OutTransactionCart::class)->name('outtransactions.cart');
Route::get('/outtransaction/cart/{product_id?}', OutTransactionCart::class)->name('outtransactions.cart');

Route::get('/out-transactions/cart/{productId?}', [App\Http\Controllers\OutTransactionController::class, 'showCart'])
    ->name('outtransactions.cart');
Route::post('/out-transactions/cart/add', [App\Http\Controllers\OutTransactionController::class, 'addToCart'])
    ->name('outtransactions.cart.add');

Route::get('/out-transactions/cart/{productId?}', OutTransactionCart::class)
    ->name('outtransactions.cart');

Route::get('download', function(){
    return 'try report pdf';
})->name('download.tes');

Route::get('/export/intransaction-pdf', [InTransactionController::class, 'exportPDF'])
    ->name('export.intransaction.pdf');

Route::get('/intransaction/{intransaction}/detail', [InTransactionController::class, 'showDetail'])
    ->name('intransaction.detail');

Route::get('/export/outtransaction/pdf', [OutTransactionController::class, 'exportPDF'])
    ->name('export.outtransaction.pdf');

Route::get('/outtransaction/{outTransaction}/detail', [OutTransactionController::class, 'showDetail'])
    ->name('outtransaction.detail');

require __DIR__.'/auth.php';
