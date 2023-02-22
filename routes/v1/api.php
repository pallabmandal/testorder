<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\v1\BaseController;
use App\Http\Controllers\v1\Auth\AuthController;
use App\Http\Controllers\v1\Auth\ResetPasswordController;
use App\Http\Controllers\v1\User\Product\ProductController;
use App\Http\Controllers\v1\User\Order\OrderController;


Route::prefix('auth')->group(function () {
   Route::post('login', [AuthController::class, 'login']);

   Route::prefix('password')->group(function () {
   		Route::post('create', [ResetPasswordController::class, 'create']);
		Route::post('reset', [ResetPasswordController::class, 'reset']);
   });
});

Route::prefix('user')->group(function () {

	Route::prefix('products')->group(function () {
		Route::get('get', [ProductController::class, 'index']);
	});

	Route::group(['middleware' => ['auth:api', 'hasUserRole']] , function () {
		Route::prefix('orders')->group(function () {
			Route::get('show', [OrderController::class, 'showClosedOrder']);
			Route::get('show-cart', [OrderController::class, 'showOpenOrder']);
			Route::get('get-open', [OrderController::class, 'getOpenOrder']);
			Route::get('get-past', [OrderController::class, 'getClosedOrder']);

			Route::post('add-item', [OrderController::class, 'addItemToOrder']);

			Route::post('place-order', [OrderController::class, 'placeOrder']);
		});
	});
});

