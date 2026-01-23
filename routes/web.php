<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('home');
});


Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
