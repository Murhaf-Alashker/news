<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/adminLogin', function () {
    $admin = \App\Models\Admin::first();
    return $admin->createToken('admin-token',['api-admin'])->plainTextToken;
}); //done
Route::post('/login', [UserController::class, 'login']); // done

Route::prefix('posts')->group(function () {
    //Route::get('/',[]);
    Route::get('/{post}',[PostController::class,'show'])->middleware('auth:api-user,api-admin'); //done
    Route::middleware('auth:api-user')->group(function () {
        Route::post('/',[PostController::class,'store']);//done
        Route::put('/{post}',[PostController::class,'update']);//done
        Route::delete('/{post}',[PostController::class,'destroy']);//done
        Route::get('/{post}/comments',[CommentController::class,'getComments']);//done
        Route::post('/{post}/comments',[CommentController::class,'store']);//done
        Route::put('/{post}/comments/{comment}',[CommentController::class,'update']);//done
        Route::delete('/{post}/comments/{comment}',[CommentController::class,'destroy']);//done

    });

    Route::middleware('auth:api-admin')->group(function () {
        Route::put('comments/{comment}',[CommentController::class,'changeStatus']);

        Route::put('/{post}/changePostStatus',[PostController::class,'changePostStatus']);
        Route::put('/{post}/changeFeatureStatus',[PostController::class,'changeFeatureStatus']);
        Route::put('/{post}/changeCommentAbility',[PostController::class,'changeCommentAbility']);

        Route::post('setting/update',[SettingController::class,'update']);

        Route::prefix('/categories')->group(function () {
            Route::post('/',[CategoryController::class,'store']);
            Route::put('/{category}',[CategoryController::class,'update']);
            Route::put('/{category}/changeStatus',[CategoryController::class,'changeStatus']);
        });

    });
});
