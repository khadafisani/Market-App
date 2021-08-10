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

        Route::get('logout', [Auth\AuthController::class, 'logout']);

        //Admin only!;
        Route::group(['middleware' => 'user:admin'],function()
        {
            Route::post('adduser', [Auth\AuthController::class, 'addUser']);
            Route::get('users', [Auth\AuthController::class, 'users']);
            Route::post('user/update/{id}', [Auth\AuthController::class, 'updateUser']);
            Route::get('user/delete/{id}', [Auth\AuthController::class, 'deleteUser']);
        });

        Route::group(['middleware' => 'user:gudang'], function()
        {
            Route::post('product', [ProductController::class, 'store']);
            Route::get('products', [ProductController::class, 'product']);
            Route::get('product/delete/{id}', [ProductController::class, 'destroy']);
            Route::get('product/{id}', [ProductController::class, 'specific']);
            Route::post('product/{id}', [ProductController::class, 'update']);
        });

        Route::group(['middleware' => 'user:kasir'], function()
        {
            Route::post('member', [MemberController::class, 'store']);
            Route::post('member/update', [MemberController::class, 'update']);
            Route::get('member/delete/{id}', [MemberController::class, 'delete']);
            Route::get('members', [MemberController::class, 'members']);
            Route::post('member/check', [MemberController::class, 'getMember']);
        });
    });

});
