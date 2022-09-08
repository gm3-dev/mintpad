<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\MintController;
use App\Http\Controllers\GeneratorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Back-end URL only
Route::domain(config('app.url'))->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->middleware('guest');

    Route::group(['middleware' => ['auth']], function () {
        // Collections
        Route::resource('collections', CollectionController::class);
        Route::get('collections/{collection}/fetch', [CollectionController::class, 'fetch'])->name('collections.fetch');
        Route::get('collections/{collection}/collection', [CollectionController::class, 'collection'])->name('collections.collection');
        // Route::get('collections/{collection}/image/{filename}', [CollectionController::class, 'image'])->name('collections.image');
        Route::post('collections/{collection}/whitelist', [CollectionController::class, 'whitelist'])->name('collections.whitelist');

        // User
        Route::get('profile', [UserController::class, 'profile'])->name('users.profile');
        Route::put('profile', [UserController::class, 'update'])->name('users.update');
        Route::get('invoices', [UserController::class, 'invoices'])->name('users.invoices');
        Route::get('invoices/{invoice_id}', [UserController::class, 'download'])->name('users.download');

        // Tutorials
        Route::get('tutorials', function () {
            return view('tutorials.index');
        })->name('tutorials');

        // Support
        Route::get('support', function () {
            return view('support.index');
        })->name('support');

        // NFT generator
        Route::get('generator', [GeneratorController::class, 'index'])->name('generator.index');
        Route::post('generator/create', [GeneratorController::class, 'create'])->name('generator.create');
        Route::post('generator/upload', [GeneratorController::class, 'upload'])->name('generator.upload');
        Route::get('generator/download', [GeneratorController::class, 'download'])->name('generator.download');
    });
});

// Mint page URL only
Route::domain(config('app.mint_url'))->group(function () {
    // Mint layout
    Route::get('mint/{permalink}', [MintController::class, 'mint'])->name('mint.index');
    Route::get('mint/{collection_id}/fetch', [MintController::class, 'fetch'])->name('mint.fetch');
});

// All domains
Route::get('data/blockchains', [DataController::class, 'blockchains'])->name('data.blockchains');

// Auth routes
require __DIR__.'/auth.php';

// Fallback aka 404
Route::fallback(function () {
    return view('errors.404');
});
