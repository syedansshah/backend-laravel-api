<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('signup', [AuthController::class, 'signup']);
// Route::group(['middleware' => 'jwt'], function () {
    Route::get('news-api', [NewsController::class , 'getFromNewsAPI']);
    Route::get('the-guardian', [NewsController::class , 'getFromTheGuardian']);
    Route::get('ny-times', [NewsController::class , 'getFromNewYorkTimes']);
    Route::get('feed', [NewsController::class , 'getFeeds']);

// });
