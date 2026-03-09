<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return "hola desde ruta web";
});



require __DIR__ . '/settings.php';
