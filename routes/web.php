<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionsContoller;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\PackController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {
//pack

    Route::get('/packs', [PackController::class, 'index'])
        ->name('packs.index');

    Route::get('/packs/create', [PackController::class, 'create'])
        ->name('packs.create');

    Route::delete('/packs/{pack}', [PackController::class, 'destroy'])
        ->name('destroy.pack');

    Route::post('/packs', [PackController::class, 'store'])
        ->name('packs.store');

//code

    Route::get('/create_code/{pack}', [CodeController::class, 'create'])
        ->name('create.code');

    Route::get('/packs/{pack}/codes', [CodeController::class, 'index'])
        ->name('show.code');

    Route::get('/packs/{pack}/codes/{code}/edit', [CodeController::class, 'edit'])
        ->name('edit.code');

    Route::put('/packs/{pack}/codes/{code}/edit', [CodeController::class, 'update'])
        ->name('update.code');

    Route::delete('/packs/{pack}/{code}', [CodeController::class, 'destroy'])
        ->name('destroy.code');

    Route::post('/create_code/{pack}', [CodeController::class, 'store'])
        ->name('store.code');
});


//auth
Route::middleware('guest')->group(function () {
    //guest po defaultu vraca na home page

    Route::post('/login',[SessionsContoller::class,'store']);

    Route::get('/login',[SessionsContoller::class,'create'])->name('login');
    // zbog name('login') ne mora da ima u bootstrap/app definisan $middleware->redirectGuestsTo('/login')!!!

    Route::get('/register',[RegisteredUserController::class,'create']);

    Route::post('/register',[RegisteredUserController::class,'store']);

});
    Route::delete('/logout',[SessionsContoller::class,'destroy'])->middleware('auth');


