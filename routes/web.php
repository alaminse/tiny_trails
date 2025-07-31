<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('backend.dashboard');
})->name('admin.dashboard');
Route::get('/users', function () {
    return view('backend.dashboard');
})->name('admin.users.create');
