<?php

use App\Http\Controllers\filecontroller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Models\Project;
use App\Models\UserProject;

Route::view('/', 'welcome')->name('home');





Route::get('/team', [TeamController::class, 'index'])->name('index')->middleware(['auth', 'admin']);

Route::get('/team-add', function () {
    return view('team.add');
})->name('team-add')->middleware(['auth', 'admin']);

Route::post('/team-store', [TeamController::class, 'store'])->name('team-store')->middleware(['auth', 'admin']);


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


Route::post('/files/upload', [filecontroller::class, 'upload'])->name('files.upload')->middleware('auth');

Route::get('/project_details/{id}', [ProjectController::class, 'project_details'])->name('project_details')->middleware('auth');
Route::patch('/project_details/{id}/status', [ProjectController::class, 'update_status'])->name('project_details.status')->middleware('auth');

require __DIR__ . '/settings.php';
