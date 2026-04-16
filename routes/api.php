<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/adminLogin', function () {
    $admin = \App\Models\Admin::first();
    return $admin->createToken('admin-token',['api-admin'])->plainTextToken;
}); //done
Route::post('/login', [UserController::class, 'login']); // done

Route::get('/', [PostController::class, 'homePage'])->middleware('auth:api-user,api-admin'); //done;
Route::get('/categories', [CategoryController::class, 'index'])->middleware('auth:api-user,api-admin'); //done;
Route::get('/categories/onlyNameAndSlug', [CategoryController::class, 'onlyNameAndSlug'])->middleware('auth:api-user'); //done;

Route::prefix('/posts')->group(function () {
    //Route::get('/',[]);
    Route::get('/{post}',[PostController::class,'show'])->middleware('auth:api-user,api-admin'); //done

    Route::middleware('auth:api-user')->group(function () {
        Route::post('/',[PostController::class,'store']);//done
        Route::put('/{post}',[PostController::class,'update']);//done
        Route::delete('/{post}',[PostController::class,'destroy']);//done
        Route::get('/{post}/relatedPosts',[PostController::class,'relatedPosts'])->middleware('auth:api-user,api-admin');
        Route::get('/{post}/comments',[CommentController::class,'getComments']);//done
        Route::post('/{post}/comments',[CommentController::class,'store']);//done
        Route::put('/{post}/comments/{comment}',[CommentController::class,'update']);//done
        Route::delete('/{post}/comments/{comment}',[CommentController::class,'destroy']);//done

        Route::post('/contact_us',[ContactUsController::class,'store']);
    });


});

Route::middleware('auth:api-admin')->group(function () {

    Route::put('comments/{comment}',[CommentController::class,'changeStatus']);

    Route::prefix('/posts')->group(function () {
        Route::put('/{post}/changePostStatus',[PostController::class,'changePostStatus']);
        Route::put('/{post}/changeFeatureStatus',[PostController::class,'changeFeatureStatus']);
        Route::put('/{post}/changeCommentAbility',[PostController::class,'changeCommentAbility']);
    });

    Route::post('setting/update',[SettingController::class,'update']);

    Route::prefix('/categories')->group(function () {
        Route::post('/',[CategoryController::class,'store']);
        Route::put('/{category}',[CategoryController::class,'update']);
        Route::put('/{category}/changeStatus',[CategoryController::class,'changeStatus']);
    });

    Route::get('/contacts',[ContactUsController::class,'index']);
    Route::get('/{user}/changStatus',[UserController::class,'pinOrUnpinUser']);

});

Route::prefix('/users')->group(function () {
    Route::middleware('auth:api-user')->group(function () {
        Route::put('/',[UserController::class,'update']);
        Route::get('/profile',[UserController::class,'showProfileForOwner']);
        Route::post('/changImage',[UserController::class,'changeImage']);
    });
    Route::get('/profile/{user}',[UserController::class,'showProfile'])->middleware('auth:api-user,api-admin');
    Route::get('/{user}/posts',[UserController::class,'getUserPosts'])->middleware('auth:api-user,api-admin');

});

