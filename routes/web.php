<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MaterialController;

Route::middleware('guest')->group(function() {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);


    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::resource('roles', RoleController::class)
        ->except(['destroy']);

    Route::patch('/roles/{role}/deactivate', [RoleController::class, 'deactivate'])
        ->name('roles.deactivate');

    Route::patch('/roles/{role}/activate', [RoleController::class, 'activate'])
        ->name('roles.activate');

    Route::get('/materials/create', [MaterialController::class, 'create'])
        ->name('materials.create');

    Route::get('/materials/create/{materialType}', [MaterialController::class, 'form'])
        ->name('materials.form');

    Route::post('/materials/create/{materialType}', [MaterialController::class, 'store'])
        ->name('materials.store');

});

Route::redirect('/','/login');

