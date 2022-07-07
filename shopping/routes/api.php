<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartDetailController;
use App\Http\Controllers\ReviewController;
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

// product
Route::get('/products',[ProductController::class,'index']);
Route::post('/products',[ProductController::class,'store']);
Route::get('/products/{product}',[ProductController::class,'show']);
Route::put('/products/{product}',[ProductController::class,'update']);
Route::delete('/products/{product}',[ProductController::class,'destroy']);
Route::post('/products/search',[ProductController::class,'search']);
Route::get('/products/info/types',[ProductController::class,'productType']);

// review
Route::get('/reviews/{review}',[ReviewController::class,'show']);
Route::post('/reviews',[ReviewController::class,'store']);
Route::put('/reviews/{review}',[ReviewController::class,'update']);
Route::delete('/reviews/{review}',[ReviewController::class,'destroy']);
Route::post('/reviews/search',[ReviewController::class,'search']);

// cart
Route::post('/carts',[CartController::class,'store']);
Route::get('/carts/{cart}',[CartController::class,'show']);
Route::delete('/carts/{product}',[CartController::class,'destroy']);
Route::post('/carts/search',[CartController::class,'search']);


// cart/order details

Route::post('/carts/detail',[CartDetailController::class,'store']);
Route::get('/carts/detail/{id}',[CartDetailController::class,'show']);
Route::put('/carts/detail/{id}',[CartDetailController::class,'update']);
Route::delete('/carts/detail/{id}',[CartDetailController::class,'destroy']);
Route::post('/carts/detail/search',[CartDetailController::class,'search']);

// order
Route::post('/orders',[OrderController::class,'store']);
Route::get('/orders/{order}',[OrderController::class,'show']);
Route::put('/orders/{order}',[OrderController::class,'update']);
Route::delete('/orders/{order}',[OrderController::class,'destroy']);
Route::post('/orders/search',[OrderController::class,'search']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
