<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('accounts', AccountController::class);
    Route::post('accounts/{account:uuid}/transaction', [AccountController::class, 'transaction']);
    Route::post('accounts/transfer-to', [AccountController::class, 'transferTo']);
    Route::get('accounts/{account:uuid}/balances', [AccountController::class, 'getBalances']);
    Route::get('accounts/{account:uuid}/transfer-history', [AccountController::class, 'getTransferHistory']);
});

