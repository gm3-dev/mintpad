<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\MintController;
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
    // App layout
    Route::resource('collections', CollectionController::class);
    Route::get('collections/{collection}/fetch', [CollectionController::class, 'fetch'])->name('collections.fetch');
    Route::get('collections/{collection}/collection', [CollectionController::class, 'collection'])->name('collections.collection');
    Route::post('collections/{collection}/upload', [CollectionController::class, 'upload'])->name('collections.upload');
    Route::get('collections/{collection}/image/{filename}', [CollectionController::class, 'image'])->name('collections.image');
    Route::post('collections/{collection}/whitelist', [CollectionController::class, 'whitelist'])->name('collections.whitelist');

    Route::get('tutorials', function () {
        return view('tutorials.index');
    })->name('tutorials');
    Route::get('support', function () {
        return view('support.index');
    })->name('support');
});

// Mint layout
Route::get('mint/{collection}', [MintController::class, 'mint'])->name('mint.index');
Route::get('mint/{collection}/fetch', [MintController::class, 'fetch'])->name('mint.fetch');

require __DIR__.'/auth.php';
