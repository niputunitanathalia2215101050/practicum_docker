<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\confirmationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::get('get_payment', [PaymentController::class, 'get_payment'])->middleware('auth:sanctum');
Route::get('show_payment', [PaymentController::class, 'show_payment'])->middleware('auth:sanctum');
Route::post('{payment}/update_payment', [PaymentController::class, 'update_payment'])->middleware('auth:sanctum');
Route::post('create_payment', [PaymentController::class, 'create_payment'])->middleware('auth:sanctum');
Route::delete('{payment}/delete_payment', [PaymentController::class, 'delete_payment'])->middleware('auth:sanctum');
Route::post('{confirmation}/update_confirmation', [ConfirmationController::class, 'update_confirmation'])->middleware('auth:sanctum');
Route::post('create_confirmation', [ConfirmationController::class, 'create_confirmation'])->middleware('auth:sanctum');
Route::delete('{payment}/delete_confirmation', [ConfirmationController::class, 'delete_confirmation'])->middleware('auth:sanctum');
Route::post('password_email', [AuthController::class, 'password_email'])->middleware('auth:sanctum');
Route::post('password_reset', [AuthController::class, 'password_reset'])->middleware('auth:sanctum');
Route::post('/edituser/{user}', [AuthController::class,'editUpdate'])->middleware('auth:sanctum');
Route::delete('/delete/{user}', [AuthController::class,'deleteUser'])->middleware('auth:sanctum');