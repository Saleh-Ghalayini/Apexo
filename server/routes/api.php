<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    // Routes for EMPLOYEE, MANAGER, HR
    Route::group([
        'prefix' => 'employee',
        'middleware' => ['auth:api', 'role:employee,manager,hr']
    ], function () {});

    // Routes for MANAGER, HR
    Route::group([
        'prefix' => 'manager',
        'middleware' => ['auth:api', 'role:manager,hr']
    ], function () {});

    // Routes for HR only
    Route::group([
        'prefix' => 'hr',
        'middleware' => ['auth:api', 'role:hr']
    ], function () {});

    // Common routes for all authenticated users
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
});
