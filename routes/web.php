<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MagazinController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ParametreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UniteController;
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
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'search'])->name('search');

    // Articles
    Route::name('articles.')->prefix('articles')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/search', [ProductController::class, 'search'])->name('search');
        Route::post('/', [ProductController::class, 'store'])->name('store')->middleware('can:admin');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update')->middleware('can:admin');
        Route::post('/delete-products', [ProductController::class, 'deleteProducts'])->name('delete.products')->middleware('can:admin');
        Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('delete.product')->middleware('can:admin');
        Route::get('/{product}', [ProductController::class, 'view'])->name('view');
        Route::post('/{product}/change-image', [ProductController::class, 'changeImage'])->name('changeImage');
        Route::delete('/{product}/delete-image', [ProductController::class, 'deleteImage'])->name('deleteImage');

        Route::get('/{product}/stocks', [ProductController::class, 'stocks'])->name('stocks');
        Route::get('/{product}/docs', [ProductController::class, 'docs'])->name('docs');
        Route::post('/{product}/upload-files', [ProductController::class, 'uploadFiles'])->name('uploadFiles');
        Route::post('/{product}/delete-files', [ProductController::class, 'deleteFiles'])->name('deleteFiles');
        Route::get('/{product}/operations', [ProductController::class, 'operations'])->name('operations');
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
    Route::get('/parametres/entreprise', [ParametreController::class, 'entreprise'])->name('entreprise');
    Route::name('parametres.')->prefix('parametres')->middleware('can:admin')->group(function () {
        Route::put('/entreprise', [ParametreController::class, 'update'])->name('update');
        Route::get('/profil-organisation', [ParametreController::class, 'index'])->name('index');
        Route::post('/entreprise/change-image', [ParametreController::class, 'changeImage'])->name('changeImage');
        Route::delete('/entreprise/delete-image', [ParametreController::class, 'deleteImage'])->name('deleteImage');

        //Unités
        Route::get('/unites', [UniteController::class, 'index']);
        Route::post('/unites', [UniteController::class, 'store']);
        Route::put('/unites/{unite}', [UniteController::class, 'update']);
        Route::post('/unites/delete-unites', [UniteController::class, 'deleteUnites']);
    });

    // Operation
    Route::name('operations.')->prefix('operations')->middleware('can:gerant')->group(function () {
        Route::get('/', [OperationController::class, 'index'])->name('index');
        Route::get('/ajouter', [OperationController::class, 'create'])->name('create');
        Route::post('/', [OperationController::class, 'store'])->name('store');
        Route::get('/{operation}', [OperationController::class, 'show'])->name('show');
        Route::get('/{operation}/modifier', [OperationController::class, 'edit'])->name('edit');
        Route::put('/{operation}/update', [OperationController::class, 'update'])->name('update');
        Route::post('/delete', [OperationController::class, 'destroy'])->name('destroy');
        Route::post('/{operation}/validate', [OperationController::class, 'validateOperation'])->name('validate');

        Route::get('/{operation}/docs', [OperationController::class, 'docs'])->name('docs');
        Route::post('/{operation}/upload-files', [OperationController::class, 'uploadFiles'])->name('uploadFiles');
        Route::post('/{operation}/delete-files', [OperationController::class, 'deleteFiles'])->name('deleteFiles');
    });
    
    // Clients
    Route::name('clients.')->prefix('clients')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/ajouter', [CustomerController::class, 'create'])->name('create')->middleware('can:gerant');
        Route::post('/', [CustomerController::class, 'store'])->name('store')->middleware('can:gerant');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show')->middleware('can:gerant');
        Route::get('/{customer}/modifier', [CustomerController::class, 'edit'])->name('edit')->middleware('can:gerant');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update')->middleware('can:gerant');
        Route::post('/delete-clients', [CustomerController::class, 'deleteClients'])->name('delete.clients')->middleware('can:gerant');
        Route::get('/{customer}/factures', [CustomerController::class, 'factures'])->name('factures');
    });
    
    // Factures
    Route::name('factures.')->prefix('factures')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/ajouter', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/modifier', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    });
});
