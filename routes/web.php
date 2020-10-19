<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UsersController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('{path}', 'HomeController@index')->where('path', '([a-zA-Z0-9_\-\./]+)?');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });
    Route::get('/', function () {
        return Inertia\Inertia::render('Dashboard');
    })->name('dashboard');

    Route::name('articles.')->prefix('articles')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store')->middleware('can:admin');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update')->middleware('can:admin');
        Route::post('/delete-products', [ProductController::class, 'deleteProducts'])->name('delete.products')->middleware('can:admin');
        Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('delete.product')->middleware('can:admin');
        Route::get('/{product}', [ProductController::class, 'view'])->name('view');
    });

    Route::name('users.')->prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index')->middleware('can:admin');
        Route::post('/', [UsersController::class, 'store'])->name('store')->middleware('can:admin');
        Route::put('/{user}', [UsersController::class, 'update'])->name('update')->middleware('can:admin');
        Route::post('/delete-users', [UsersController::class, 'deleteProducts'])->name('delete.products')->middleware('can:admin');
    });
});
