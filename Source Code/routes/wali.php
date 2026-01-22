<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:wali'])
    ->prefix('wali')
    ->name('wali.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('wali.dashboard'))->name('dashboard');
    });
