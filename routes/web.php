<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/{any?}', function () {
    return response()->file(public_path('admin/index.html'));
})->where('any', '.*');

Route::get('/{any?}', function () {
    return response()->file(public_path('app/index.html'));
})->where('any', '^(?!api|admin).*$');
