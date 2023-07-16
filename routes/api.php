<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('customers', CustomerController::class);
});

