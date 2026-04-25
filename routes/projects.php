<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;



Route::get('/', [ProjectController::class, 'index'])->name('Projects');

Route::get('/favorites', [ProjectController::class, 'favorites'])->name('favorites')->middleware('auth');

Route::get('/create/{id}', [ProjectController::class, 'project_create'])->name('project_create')->middleware('auth');

Route::get('/drop/{id}', [ProjectController::class, 'project_drop'])->name('project_drop')->middleware('auth');
