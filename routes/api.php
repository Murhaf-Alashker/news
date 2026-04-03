<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
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
        Route::get('/{post}/comments',[CommentController::class,'getComments']);
        Route::post('/{post}/comments',[CommentController::class,'store']);
        Route::put('/{post}/comments/{comment}',[CommentController::class,'update']);
        Route::delete('/{post}/comments/{comment}',[CommentController::class,'destroy']);

    });

    Route::middleware('auth:api-admin')->group(function () {
        Route::put('comments/{comment}',[CommentController::class,'changeStatus']);

        Route::put('/{post}/changePostStatus',[PostController::class,'changePostStatus']);
        Route::put('/{post}/changeFeatureStatus',[PostController::class,'changeFeatureStatus']);
        Route::put('/{post}/changeCommentAbility',[PostController::class,'changeCommentAbility']);

        Route::post('setting/update',[SettingController::class,'update']);
    });
});
