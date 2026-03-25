<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;



Route::get('/', [ProjectController::class, 'index'])->name('Projects');

Route::get('/create/{id}', [ProjectController::class, 'project_create'])->name('project_create')->middleware('auth');
