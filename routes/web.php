<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OpacController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MyReservationController;
use App\Http\Controllers\StaffReservationController;
use App\Http\Controllers\LoanTermController;
use App\Http\Controllers\GlobalRuleController;
use App\Http\Controllers\CirculationController;

Route::get('/opac', [OpacController::class, 'index'])->name('opac.index');
Route::get('/opac/materials/{material}', [OpacController::class, 'show'])->name('opac.materials.show');
Route::get('/reservations/{reservation}/confirm/{token}', [ReservationController::class, 'confirmation'])
    ->middleware('signed')->name('reservations.confirmation');
Route::post('/reservations/{reservation}/confirm/{token}', [ReservationController::class, 'confirm'])
    ->middleware('signed')->name('reservations.confirm');
Route::post('/reservations/{reservation}/reject/{token}', [ReservationController::class, 'reject'])
    ->middleware('signed')->name('reservations.reject');

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

    Route::get('/loan-terms', [LoanTermController::class, 'index'])
        ->name('loan-terms.index');

    Route::get('/loan-terms/{materialType}/edit', [LoanTermController::class, 'edit'])
        ->name('loan-terms.edit');

    Route::put('/loan-terms/{materialType}', [LoanTermController::class, 'update'])
        ->name('loan-terms.update');

    Route::get('/global-rules', [GlobalRuleController::class, 'edit'])
        ->name('global-rules.edit');

    Route::put('/global-rules', [GlobalRuleController::class, 'update'])
        ->name('global-rules.update');

    Route::get('/circulations/create', [CirculationController::class, 'create'])
        ->name('circulations.create');

    Route::post('/circulations', [CirculationController::class, 'store'])
        ->name('circulations.store');

    Route::get('/circulations', [CirculationController::class, 'index'])
        ->name('circulations.index');

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

    Route::get('/opac/materials/{material}/reserve', [ReservationController::class, 'create'])
        ->name('reservations.create');
    Route::post('/opac/materials/{material}/reserve', [ReservationController::class, 'store'])
        ->name('reservations.store');

    Route::patch('/mis-reservas/{reservation}/cancel', [ReservationController::class, 'cancelOwn'])
        ->name('reservations.cancel-own');

    Route::get('/staff-reservations/create', [StaffReservationController::class, 'create'])
        ->name('staff-reservations.create');

    Route::post('/staff-reservations', [StaffReservationController::class, 'store'])
        ->name('staff-reservations.store');

    Route::get('/staff-reservations', [StaffReservationController::class, 'index'])
        ->name('staff-reservations.index');

    Route::get('/staff-reservations/{reservation}/cancel', [StaffReservationController::class, 'cancelForm'])
        ->name('staff-reservations.cancel-form');

    Route::patch('/staff-reservations/{reservation}/cancel', [StaffReservationController::class, 'cancel'])
        ->name('staff-reservations.cancel');

    Route::get('/mis-reservas', [MyReservationController::class, 'index'])
        ->name('reservations.mine');
});

Route::redirect('/', '/opac');

