<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Posts\Index as PostIndex;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Pages\Index as PageIndex;
use App\Livewire\Dashboard;

Route::redirect('/', 'login');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('posts', PostIndex::class)->name('posts');
    Route::get('pages', PageIndex::class)->name('pages');
    Route::get('categories', CategoryIndex::class)->name('categories');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
