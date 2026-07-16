<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderPositionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\MailConfigurationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::prefix('content')->group(function () {
    Route::get('/', [ContentController::class, 'index']);
    Route::get('/id/{id}', [ContentController::class, 'getById']);
    Route::get('/{route}', [ContentController::class, 'show']);
    Route::get('/route/get', [ContentController::class, 'route']);
});

Route::middleware('jwt')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::apiResource('clients', ClientController::class);

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/my', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::get('/{order}/positions', [OrderController::class, 'positions']);
        Route::post('/', [OrderController::class, 'store']);
    });

    Route::middleware('employee')->prefix('employee')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);

        Route::put('/orders/{order}', [OrderController::class, 'update']);
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    });

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        Route::get('/mail/configuration', [MailConfigurationController::class, 'show']);
        Route::put('/mail/configuration', [MailConfigurationController::class, 'update']);

        Route::prefix('content')->group(function () {
            Route::post('/', [ContentController::class, 'store']);
            Route::put('/{id}', [ContentController::class, 'update']);
            Route::delete('/{id}', [ContentController::class, 'destroy']);
        });
    });
});
