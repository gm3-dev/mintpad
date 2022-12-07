<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\MintController;
use App\Http\Controllers\GeneratorController;
use App\Http\Controllers\ResourceController;
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

Route::domain(config('app.url'))->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->middleware('guest');

    Route::group(['middleware' => ['auth']], function () {
        /**
         * Admin routes
         */
        Route::group(['prefix' => 'admin', 'middleware' => ['admin', 'password.confirm']], function () {
            Route::name('admin.')->group(function () {
                // Dashboard
                Route::get('/', [AdminDashboardController::class, 'index']);
                Route::resource('dashboard', AdminDashboardController::class);
                // Collections
                Route::resource('collections', AdminCollectionController::class);
                // Import
                Route::resource('import', ImportController::class);
                // Invoices
                Route::resource('invoices', InvoiceController::class);
                // Invoices
                Route::resource('status', StatusController::class);
            });
        });
        
        /**
         * Customer routes
         */
        Route::resource('collections', CollectionController::class);
        Route::get('collections/{collection}/fetch', [CollectionController::class, 'fetch'])->name('collections.fetch');
        Route::get('collections/{collection}/collection', [CollectionController::class, 'collection'])->name('collections.collection');
        Route::post('collections/{collection}/whitelist', [CollectionController::class, 'whitelist'])->name('collections.whitelist');
        Route::put('collections/{collection}/mint', [CollectionController::class, 'updateMint'])->name('collections.update_mint');
        Route::put('collections/{collection}/metadata', [CollectionController::class, 'updateMetadata'])->name('collections.update_metadata');
        Route::put('collections/{collection}/claim-phases', [CollectionController::class, 'updateClaimPhases'])->name('collections.update_claim_phases');
        Route::post('collections/{collection}/thumb', [CollectionController::class, 'downloadThumb'])->name('collections.thumb');

        // User
        Route::get('profile', [UserController::class, 'profile'])->name('users.profile');
        Route::put('profile', [UserController::class, 'update'])->name('users.update');
        Route::get('invoices', [UserController::class, 'invoices'])->name('users.invoices');
        Route::get('invoices/{invoice_id}', [UserController::class, 'download'])->name('users.download');

        // NFT generator
        Route::get('generator', [GeneratorController::class, 'index'])->name('generator.index');
        Route::post('generator/create', [GeneratorController::class, 'create'])->name('generator.create');
        Route::post('generator/upload', [GeneratorController::class, 'upload'])->name('generator.upload');
        Route::get('generator/download', [GeneratorController::class, 'download'])->name('generator.download');
        Route::get('generator/status', [GeneratorController::class, 'status'])->name('generator.status');

        // Editor layout
        Route::get('editor/{collection}', [EditorController::class, 'index'])->name('editor.index');

        // Resources
        Route::post('resources/{collection}/upload', [ResourceController::class, 'upload'])->name('resources.upload');
        Route::delete('resources/{collection}/delete', [ResourceController::class, 'delete'])->name('resources.delete');
    });
});

Route::domain(config('app.mint_url'))->group(function () {
    // Mint layout
    Route::get('{permalink}', [MintController::class, 'mint'])->name('mint.index');
});

Route::get('{collection_id}/fetch', [MintController::class, 'fetch'])->name('mint.fetch');


/**
 * Global routes
 */
Route::get('data/blockchains', [DataController::class, 'blockchains'])->name('data.blockchains');

/**
 * Auth routes
 */
require __DIR__.'/auth.php';

/**
 * Fallback routes
 */
Route::fallback(function () {
    return view('errors.404');
});
