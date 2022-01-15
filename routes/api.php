<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwitController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('profile/importLastTwentyTwits', [ProfileController::class, 'importLastTwentyTwits']);

    Route::resource('twit', TwitController::class)
        ->only(['index','show','update','store','destroy']);
});

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('register', [AuthController::class, 'register'])
        ->name('auth.register');

    Route::post('login', [AuthController::class, 'login'])
        ->name('auth.login');

    Route::post('verify/email', [AuthController::class, 'emailVerify'])
        ->name('auth.emailVerify');

    Route::post('verify/phone', [AuthController::class, 'phoneVerify'])
        ->name('auth.phoneVerify');
});
