<?php

use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\auth\VerificationController;
use App\Http\Controllers\api\product\ProductController;
use App\Http\Controllers\api\product\VariationCombinationController;
use App\Http\Controllers\api\product\VariationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(VerificationController::class)->group(function () {
    Route::get('email/verify/{id}/{hash}','verifyEmail')->name('verification.verify');
    Route::post('password/email', 'sendResetLink');
    Route::post('password/reset', 'resetPassword');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('products.variations', VariationController::class);
    Route::apiResource('products.variation-combinations', VariationCombinationController::class);
});



