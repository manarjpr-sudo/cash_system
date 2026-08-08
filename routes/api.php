<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;


Route::post('/register', [AuthController::class, 'register'])
    ->name('api.register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');


    Route::prefix('v1')
        ->name('api.')
        ->group(function () {


            Route::apiResource('operations', OperationController::class);


            Route::apiResource('customers', CustomerController::class);


            Route::apiResource('approvals', ApprovalController::class);


            Route::apiResource('transactions', TransactionController::class)
                ->only([
                    'index',
                    'show'
                ]);


            Route::get('/dashboard', [DashboardController::class, 'api'])
                ->name('dashboard');

        });

});