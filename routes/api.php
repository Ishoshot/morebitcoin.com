<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/* -------------------------- Authentication Routes ------------------------- */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/reset/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('get-user', [UserController::class, 'getLoggedInUser']);
    Route::put('user/update', [UserController::class, 'update']);
    Route::post('user/verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'investment'], function () {
    Route::get('paginate', [InvestmentController::class, 'paginate']);
    Route::get('admin/paginate', [InvestmentController::class, 'index']);
    Route::get('ongoing', [InvestmentController::class, 'ongoing']);
    Route::get('chartdata', [InvestmentController::class, 'chartData']);
    Route::get('dashCardData', [InvestmentController::class, 'dashCardData']);
    Route::post('create', [InvestmentController::class, 'store']);
    Route::put('update/{investment}', [InvestmentController::class, 'update']);
    Route::get('{id}', [InvestmentController::class, 'show']);
});


Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'withdrawal'], function () {
    Route::get('all', [WithdrawalController::class, 'index']);
    Route::get('user', [WithdrawalController::class, 'userIndex']);
    Route::post('create', [WithdrawalController::class, 'store']);
    Route::put('update/{withdrawal}', [WithdrawalController::class, 'update']);
    Route::get('{id}', [WithdrawalController::class, 'show']);
});
