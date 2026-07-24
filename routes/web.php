<?php

use App\Http\Controllers\CodeController;
use App\Http\Controllers\PackController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('login', 'auth.login');

Route::view('register', 'auth.register');

Route::get('/packs', [PackController::class, 'index'])
    ->name('packs.index');

Route::get('/packs/create', [PackController::class, 'create'])
    ->name('packs.create');

Route::get('/create_code/{pack}', [CodeController::class, 'create'])
    ->name('create.code');

Route::get('/packs/{pack}/codes', [CodeController::class, 'index'])
    ->name('show.code');

Route::get('/packs/{pack}/codes/{code}/edit', [CodeController::class, 'edit'])
    ->name('edit.code');

Route::put('/packs/{pack}/codes/{code}/edit', [CodeController::class, 'update'])
    ->name('update.code');

Route::delete('/packs/{pack}', [PackController::class, 'destroy'])
    ->name('destroy.pack');

Route::delete('/packs/{pack}/{code}', [CodeController::class, 'destroy'])
    ->name('destroy.code');

Route::post('/create_code/{pack}', [CodeController::class, 'store'])
    ->name('store.code');

Route::post('/packs', [PackController::class, 'store'])
    ->name('packs.store');
