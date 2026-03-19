<?php

use App\Http\Controllers\filecontroller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\UserProject;

Route::view('/', 'welcome')->name('home');


Route::get('/team', function () {
    echo 'estos son los miembros del equipo';
    die();
    return view('pages.team');
})->name('team');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (Auth::user()->isSuperAdmin()) {
            return redirect('/sadmin');
        }
        return view('dashboard');
    })->name('dashboard');
});





Route::get('/files', function () {
    return view('superadmin.files');
})->name('files')->middleware('auth');

Route::get('/files/update', [filecontroller::class, 'updateDatabase'])->name('files.update')->middleware('auth');




Route::get('/seachProject', function () {
    return 'buscar proyectos nuevos';
})->name('seachProject')->middleware('auth');


Route::post('/files/upload', [filecontroller::class, 'upload'])->name('files.upload');

require __DIR__ . '/settings.php';
