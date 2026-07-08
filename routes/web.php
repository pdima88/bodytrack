<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureProfileIsComplete;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware(EnsureProfileIsComplete::class)->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/charts', [\App\Http\Controllers\ChartsController::class, 'index'])->name('charts');
        Route::get('/measurements/export', [\App\Http\Controllers\MeasurementController::class, 'export'])
            ->name('measurements.export');
        Route::resource('measurements', \App\Http\Controllers\MeasurementController::class)
            ->except(['show']);
    });
});

Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');
