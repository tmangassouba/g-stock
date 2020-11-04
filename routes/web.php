<?php

use App\Http\Controllers\DeviseController;
use App\Http\Controllers\MagazinController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ParametreController;
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

    // Articles
    Route::name('articles.')->prefix('articles')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store')->middleware('can:admin');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update')->middleware('can:admin');
        Route::post('/delete-products', [ProductController::class, 'deleteProducts'])->name('delete.products')->middleware('can:admin');
        Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('delete.product')->middleware('can:admin');
        Route::get('/{product}', [ProductController::class, 'view'])->name('view');
        Route::post('/{product}/change-image', [ProductController::class, 'changeImage'])->name('changeImage');
        Route::delete('/{product}/delete-image', [ProductController::class, 'deleteImage'])->name('deleteImage');
    });

    //Utilisateurs
    Route::name('users.')->prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index')->middleware('can:admin');
        Route::post('/', [UsersController::class, 'store'])->name('store')->middleware('can:admin');
        Route::put('/{user}', [UsersController::class, 'update'])->name('update')->middleware('can:admin');
        Route::post('/delete-users', [UsersController::class, 'deleteProducts'])->name('delete.products')->middleware('can:admin');
    });

    // Magazins
    Route::name('magazins.')->prefix('magazins')->group(function () {
        Route::get('/', [MagazinController::class, 'index'])->name('index')->middleware('can:admin');
        Route::post('/', [MagazinController::class, 'store'])->name('store')->middleware('can:admin');
        Route::put('/{magazin}', [MagazinController::class, 'update'])->name('update')->middleware('can:admin');
        Route::post('/delete-magazins', [MagazinController::class, 'deleteMagazins'])->name('delete.magazins')->middleware('can:admin');
    });
    
    // Devises
    Route::name('devises.')->prefix('devises')->group(function () {
        Route::get('/', [DeviseController::class, 'index'])->name('index')->middleware('can:admin');
        Route::post('/', [DeviseController::class, 'store'])->name('store')->middleware('can:admin');
        Route::put('/{devise}', [DeviseController::class, 'update'])->name('update')->middleware('can:admin');
        Route::post('/delete-devises', [DeviseController::class, 'deleteDevises'])->name('delete.devises')->middleware('can:admin');
    });

    // Parametre
    Route::get('/entreprise', [ParametreController::class, 'entreprise'])->name('entreprise')->middleware('can:admin');
    Route::put('/entreprise', [ParametreController::class, 'update'])->name('update')->middleware('can:admin');
    Route::get('/profil-organisation', [ParametreController::class, 'index'])->name('index')->middleware('can:admin');
    Route::post('/entreprise/change-image', [ParametreController::class, 'changeImage'])->name('changeImage');
    Route::delete('/entreprise/delete-image', [ParametreController::class, 'deleteImage'])->name('deleteImage');

    // Operation
    Route::name('operations.')->prefix('operations')->middleware('can:gerant')->group(function () {
        Route::get('/', [OperationController::class, 'index'])->name('index');
        Route::get('/ajouter', [OperationController::class, 'create'])->name('create');
        Route::post('/', [OperationController::class, 'store'])->name('store');
        Route::get('/{operation}', [OperationController::class, 'show'])->name('show');
        Route::get('/{operation}/modifier', [OperationController::class, 'edit'])->name('edit');
        Route::put('/{operation}/update', [OperationController::class, 'update'])->name('update');
        Route::delete('/{operation}', [OperationController::class, 'destroy'])->name('destroy');
    });
});
