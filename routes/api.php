<?php

use App\Http\Resources\CustomerResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\Devise;
use App\Models\Magazin;
use App\Models\Product;
use App\Models\Role;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return new UserResource($request->user());
});

Route::get('unites', function () {
    return Unite::all();
});

Route::get('roles', function () {
    return Role::all();
});

Route::get('devises', function () {
    return Devise::all();
});

Route::get('magazins', function () {
    return Magazin::all();
});

Route::get('produits', function (Request $request) {
    $req = Product::orderBy('designation');
    if ($request->recherche) {
        $req = $req->where('designation', 'like', '%'.$request->recherche.'%')
                    ->orWhere('code', 'like', '%'.$request->recherche.'%');
    }
    $products = $req->paginate(20);
    return ProductResource::collection($products);
});

Route::get('clients', function (Request $request) {
    $req = Customer::orderBy('name');
    if ($request->recherche) {
        $req = $req->where('name', 'like', '%'.$request->recherche.'%');
    }
    $products = $req->paginate(20);
    return CustomerResource::collection($products);
});
