<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClubController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('users', UserController::class)->middleware('auth');
Route::resource('clubs', ClubController::class)->middleware('auth');

Route::get('/set-locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ru'])) {
        session(['locale' => $locale]);
    }
    return back();
});

//Route::get('/', fn() => redirect('/login'));
Route::get('/', function () {
    return view('pages.home');
});
