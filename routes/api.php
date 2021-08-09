<?php
namespace App\Http\Controllers;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function() {
    Route::post('login', [Auth\AuthController::class, 'login']);


    Route::group(['middleware' => 'auth:api'], function(){

        //Admin only!
        Route::group(['middleware' => 'admin'],function()
        {
            Route::post('adduser', [Auth\AuthController::class, 'addUser']);
            Route::get('users', [Auth\AuthController::class, 'users']);

        });

        Route::post('product', [ProductController::class, 'store']);
        Route::get('product', [ProductController::class, 'getData']);
        Route::get('product/{id}', [ProductController::class, 'destroy']);
        Route::post('product/{id}', [ProductController::class, 'update']);
    });

});
