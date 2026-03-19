<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('superadmin.dashboard');
});





require __DIR__ . '/settings.php';
