<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\StoreController;
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

// > account
// >> V
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/existuser/{id}', [UserController::class, 'UserExist']);
Route::delete('/dropuser/{id}', [UserController::class, 'DropUser']);
// >> X
// .....

// > account's profile
// >> V
// ......
// >> X
Route::post('/user/profile', [UserController::class, 'store']);
Route::put('/user/profile/{id}', [UserController::class, 'update']);
Route::get('/user/profile/{id}', [UserController::class, 'show']);
Route::post('/user/profile/search', [UserController::class, 'search']);

// > store
// >> V
Route::get('/store', [StoreController::class, 'index']);
Route::get('/store/{id}', [StoreController::class, 'show']);
Route::post('/store/search', [StoreController::class, 'search']);
// >> X
Route::post('/store', [StoreController::class, 'store']);
Route::put('/store/{id}', [StoreController::class, 'update']);


Route::group(['middleware' => ['auth:sanctum']], function () {

    // > account
    // >> V
    Route::get('/current', [UserController::class, 'currentUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    // >> X
    // ......

    // > account's profile
    // >> V
    Route::put('/user/profile', [UserController::class, 'update']);
    // >> X
    //Route::post('/user/profile', [UserController::class, 'store']);
    //Route::get('/user/profile/{id}', [UserController::class, 'show']);
    //Route::post('/user/profile/search', [UserController::class, 'search']);

    // > store
    // >> V
    Route::put('/store', [StoreController::class, 'update']);
    // >> X
    //Route::post('/store', [StoreController::class, 'store']);
    //Route::put('/store/{id}', [StoreController::class, 'update']);

    // > contract
    Route::get('/contract', [ContractController::class, 'index']);
    Route::post('/contract', [ContractController::class, 'store']);
    Route::get('/contract/{contract}', [ContractController::class, 'show']);
    Route::put('/contract/{contract}', [ContractController::class, 'update']);
    Route::delete('/contract/{contract}', [ContractController::class, 'destroy']);
    Route::post('/contract/search', [ContractController::class, 'search']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
