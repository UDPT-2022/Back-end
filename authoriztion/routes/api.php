<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContractController;
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
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/user/search', [UserController::class, 'search']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/current', [UserController::class, 'currentUser']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/user/profile', [UserController::class, 'index']);
    Route::put('/user/profile', [UserController::class, 'update']);

    Route::get('/contract', [ContractController::class, 'index']);
    Route::post('/contract', [ContractController::class, 'store']);
    Route::put('/contract', [ContractController::class, 'update']);
    Route::delete('/contract', [ContractController::class, 'destroy']);
    Route::post('/contract/search', [ContractController::class, 'search']);
    Route::get('/contract/{id}', [ContractController::class, 'show']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
