<?php

use App\Livewire\Dashboard;
use App\Livewire\Landing;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class)
    ->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)
        ->name('dashboard');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
