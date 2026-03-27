<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'login']);

Route::prefix('posts')->group(function () {
    //Route::get('/',[]);
    Route::get('/{post}',[PostController::class,'show']);
    Route::middleware('auth:api-user')->group(function () {
        Route::post('/',[PostController::class,'store']);
        Route::put('/{post}',[PostController::class,'update']);
        Route::delete('/{post}',[PostController::class,'destroy']);
    });
});
