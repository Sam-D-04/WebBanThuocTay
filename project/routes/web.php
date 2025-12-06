<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $port = request()->getPort();

    if ($port == 8001) {
        return view('admin.admin');
    }

    return view('user.user');
});
Route::get('/admin/{any?}', function () {
    return view('admin.admin');
})->where('any', '.*');

// 2) Route cho USER SPA
Route::get('/{any?}', function () {
    return view('user.user');
})->where('any', '^(?!api|admin).*$');
