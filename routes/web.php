<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TravelPackageController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\HomeController;
use App\Models\TravelPackage;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', [HomeController::class, 'index'])
    ->name('home');
Route::get('/detail', [DetailController::class, 'index'])
    ->name('detail');
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout');
Route::get('/checkout/success', [CheckoutController::class, 'success'])
    ->name('checkout-success');

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('travel-package', TravelPackageController::class);
        // Route::get('travel-package', [TravelPackageController::class, 'index'])->name('travel-package.index');
        // Route::get('travel-package/create', [TravelPackageController::class, 'create'])->name('travel-package.create');
        // Route::post('travel-package', [TravelPackageController::class, 'store'])->name('travel-package.store');
        // Route::get('travel-package/{id}/edit', [ProductController::class, 'edit'])->name('travel-package.edit');
        // Route::put('travel-package/{id}', [ProductController::class, 'update'])->name('travel-package.update');
        // Route::delete('travel-package/{id}', [ProductController::class, 'destroy'])->name('travel-package.destroy');
    });

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
