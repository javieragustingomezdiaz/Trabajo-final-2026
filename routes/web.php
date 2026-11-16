<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OpacController;

Route::get('/opac', [OpacController::class, 'index'])->name('opac.index');
Route::get('/opac/materials/{material}', [OpacController::class, 'show'])->name('opac.materials.show');

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

    Route::get('/materials/search', [MaterialController::class, 'search'])
        ->name('materials.search');

    Route::get('/materials/{material}/edit', [MaterialController::class, 'edit'])
        ->name('materials.edit');

    Route::put('/materials/{material}', [MaterialController::class, 'update'])
        ->name('materials.update');

    Route::patch('/materials/{material}/deactivate', [MaterialController::class, 'deactivate'])
        ->name('materials.deactivate');

    Route::patch('/materials/{material}/activate', [MaterialController::class, 'activate'])
        ->name('materials.activate');

    Route::get('/materials/create', [MaterialController::class, 'create'])
        ->name('materials.create');

    Route::get('/materials/create/{materialType}', [MaterialController::class, 'form'])
        ->name('materials.form');

    Route::post('/materials/create/{materialType}', [MaterialController::class, 'store'])
        ->name('materials.store');

    Route::get('/items/create', [ItemController::class, 'create'])
    ->name('items.create');

    Route::get('/materials/{material}/items/create', [ItemController::class, 'form'])
        ->name('items.form');

    Route::post('/materials/{material}/items', [ItemController::class, 'store'])
        ->name('items.store');
    Route::get('/locations', [LocationController::class, 'index'])
        ->name('locations.index');

    Route::post('/locations', [LocationController::class, 'store'])
        ->name('locations.store');

    Route::post('/locations/{location}/containers', [LocationController::class, 'storeContainer'])
        ->name('locations.containers.store');

    Route::get('/items/search', [ItemController::class, 'search'])
        ->name('items.search');

    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])
        ->name('items.edit');

    Route::put('/items/{item}', [ItemController::class, 'update'])
        ->name('items.update');

    Route::patch('/items/{item}/deactivate', [ItemController::class, 'deactivate'])
        ->name('items.deactivate');

    Route::patch('/items/{item}/activate', [ItemController::class, 'activate'])
        ->name('items.activate');
});

Route::redirect('/', '/opac');

