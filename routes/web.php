<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\MintController;
use App\Http\Controllers\GeneratorController;
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

Route::get('/', [AuthenticatedSessionController::class, 'create'])->middleware('guest');

Route::group(['middleware' => ['auth']], function () {
    // Collections
    Route::resource('collections', CollectionController::class);
    Route::get('collections/{collection}/fetch', [CollectionController::class, 'fetch'])->name('collections.fetch');
    Route::get('collections/{collection}/collection', [CollectionController::class, 'collection'])->name('collections.collection');
    // Route::get('collections/{collection}/image/{filename}', [CollectionController::class, 'image'])->name('collections.image');
    Route::post('collections/{collection}/whitelist', [CollectionController::class, 'whitelist'])->name('collections.whitelist');

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
});

// Mint layout
Route::get('mint/{collection}', [MintController::class, 'mint'])->name('mint.index');
Route::get('mint/{collection_id}/fetch', [MintController::class, 'fetch'])->name('mint.fetch');

require __DIR__.'/auth.php';
