<?php

use App\Http\Resources\UserResource;
use App\Models\Devise;
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
