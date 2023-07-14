<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\UpcomingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\MintController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::domain(config('app.url'))->group(function () {
    /**
     * Fortify routes
     */
    // require __DIR__.'/fortify.php';

    Route::get('/', fn () => redirect('/login'));

    Route::group(['middleware' => ['auth']], function () {
        /**
         * Admin routes
         */
        Route::group(['prefix' => 'admin', 'middleware' => ['admin', 'password.confirm']], function () {
            Route::name('admin.')->group(function () {
                // Dashboard
                Route::get('/', [DashboardController::class, 'index']);
                Route::get('/get-wallet-balances', [DashboardController::class, 'getWalletBalances']);
                Route::resource('dashboard', DashboardController::class);
                // Collections
                Route::resource('collections', AdminCollectionController::class);
                // Users
                Route::resource('users', AdminUserController::class);
                // Import
                Route::resource('import', ImportController::class);
                // Invoices
                Route::resource('invoices', InvoiceController::class);
                // Upcoming
                Route::resource('upcoming', UpcomingController::class);
                // Status
                Route::resource('status', StatusController::class);
            });
        });

        // Collections
        Route::resource('collections', CollectionController::class);
        Route::put('collections/{collection}/metadata', [CollectionController::class, 'updateMetadata'])->name('collections.update-metadata');
        Route::put('collections/{collection}/mint', [CollectionController::class, 'updateMint'])->name('collections.update-mint');
        Route::post('collections/{collection}/whitelist', [CollectionController::class, 'whitelist'])->name('collections.whitelist');
        Route::post('collections/{collection}/thumb', [CollectionController::class, 'downloadThumb'])->name('collections.thumb');

        // User
        Route::get('two-factor-authentication', [UserController::class, 'twoFactorAuthSettings'])->name('users.2fa');
        Route::get('profile', [UserController::class, 'profile'])->name('users.profile');
        Route::put('profile', [UserController::class, 'update'])->name('users.update');

        // Editor layout
        Route::get('mint-editor/{collection}/{token?}', [MintController::class, 'mint'])->name('editor.mint');
        Route::get('embed-editor/{collection}', [EditorController::class, 'embed'])->name('editor.embed');

        // Resources
        Route::post('resources/{collection}/upload', [ResourceController::class, 'upload'])->name('resources.upload');
        Route::delete('resources/{collection}/delete', [ResourceController::class, 'delete'])->name('resources.delete');

        // Validation
        // Route::post('validate', [ValidationController::class, 'index'])->name('validate.index');

        // Messenger
        // Route::post('messenger', [MessengerController::class, 'index'])->name('messenger.index');
    });
});

Route::domain(config('app.mint_url'))->group(function () {
    // Mint
    Route::get('{permalink}/{token?}', [MintController::class, 'mint'])->name('mint.index');
    Route::get('{permalink}/burn/{token?}', [MintController::class, 'burn'])->name('mint.burn');
});

Route::domain(config('app.embed_url'))->group(function () {
    // Mint
    Route::get('{address}/{token?}', [MintController::class, 'embed'])->name('mint.embed');
    Route::get('{address}/burn/{token?}', [MintController::class, 'embedBurn'])->name('mint.embed-burn');
});

Route::get('collection/{collection_id}/fetch', [MintController::class, 'fetch'])->name('mint.fetch');

/**
 * Fallback routes
 */
Route::fallback(function () {
    return Inertia::render('Errors/Index', ['status' => 404]);
});
