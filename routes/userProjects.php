<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('userProjects');
})->name('userProjects');



require __DIR__ . '/settings.php';
